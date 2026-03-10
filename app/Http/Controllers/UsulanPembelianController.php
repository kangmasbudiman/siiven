<?php

namespace App\Http\Controllers;

use App\Models\ApprovalUsulanPembelian;
use App\Models\DetailUsulanPembelian;
use App\Models\LampiranUsulanPembelian;
use App\Models\Ruangan;
use App\Models\UsulanPembelian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UsulanPembelianController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = UsulanPembelian::with(['ruangan', 'pembuat'])->latest();

        if ($user->hakAkses == '1') {
            // Admin: lihat semua ruangan
        } elseif ($user->approval_level) {
            // Approver (Pemeriksa/Konfirmator/Kabag/Direktur): lihat semua ruangan
            // karena mereka perlu mereview usulan dari semua unit
        } else {
            // Ka. Unit: hanya lihat usulan dari ruangannya sendiri
            if ($user->ruangan_id) {
                $query->where('ruangan_id', $user->ruangan_id);
            } else {
                // User belum punya ruangan, tampilkan kosong
                $query->whereRaw('1 = 0');
            }
        }

        $usulans = $query->get();

        // Hitung pending approval untuk approver
        $pendingCount = 0;
        if ($user->approval_level) {
            $pendingStatus = $this->getStatusRequiringLevel($user->approval_level);
            if ($pendingStatus) {
                $pendingCount = UsulanPembelian::where('status', $pendingStatus)->count();
            }
        }

        return view('usulan-pembelian.index', compact('usulans', 'pendingCount'));
    }

    public function data()
    {
        $user = Auth::user();

        $query = UsulanPembelian::with(['ruangan', 'pembuat'])->latest();

        if ($user->hakAkses == '1') {
            // Admin: semua
        } elseif ($user->approval_level) {
            // Approver: semua
        } else {
            if ($user->ruangan_id) {
                $query->where('ruangan_id', $user->ruangan_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $usulans = $query->get();

        $pendingCount = 0;
        if ($user->approval_level) {
            $pendingStatus = $this->getStatusRequiringLevel($user->approval_level);
            if ($pendingStatus) {
                $pendingCount = UsulanPembelian::where('status', $pendingStatus)->count();
            }
        }

        $rows = $usulans->map(function ($u) use ($user) {
            $aksi = '<a href="' . route('usulan.show', $u->id) . '" class="btn btn-info btn-sm" title="Detail"><i class="fa fa-eye"></i></a>';

            if ($u->status === 'draft' && $u->dibuat_oleh == $user->idUser) {
                $aksi .= ' <form action="' . route('usulan.submit', $u->id) . '" method="POST" class="d-inline form-ajukan" data-nomor="' . $u->nomor_usulan . '">'
                    . csrf_field()
                    . '<button type="submit" class="btn btn-warning btn-sm" title="Ajukan Usulan"><i class="fa fa-paper-plane"></i> Ajukan</button>'
                    . '</form>';
            }

            if ($u->status === 'disetujui') {
                $aksi .= ' <a href="' . route('usulan.pdf', $u->id) . '" class="btn btn-danger btn-sm" title="Download PDF" target="_blank"><i class="fa fa-file-pdf-o"></i></a>';
                $aksi .= ' <a href="' . route('usulan.wa', $u->id) . '" class="btn btn-success btn-sm" title="Kirim WA" target="_blank"><i class="fa fa-whatsapp"></i></a>';
            }

            return [
                'nomor_usulan'        => $u->nomor_usulan,
                'tanggal'             => $u->tanggal_pengajuan->format('d/m/Y'),
                'tanggal_order'       => $u->tanggal_pengajuan->format('Y-m-d'),
                'ruangan'             => $u->ruangan->nama_ruangan ?? '-',
                'penanggung_jawab'    => $u->nama_penanggung_jawab,
                'status_label'        => $u->status_label,
                'status_class'        => $u->status_class,
                'pembuat'             => $u->pembuat->nama ?? '-',
                'aksi'                => $aksi,
            ];
        });

        return response()->json(['rows' => $rows, 'pendingCount' => $pendingCount]);
    }

    public function create()
    {
        $user = Auth::user();

        // Admin bisa pilih ruangan, user biasa otomatis dari profil
        if ($user->hakAkses == '1') {
            $ruangans = Ruangan::where('is_active', 1)->get();
            $userRuangan = null;
        } else {
            $ruangans = null;
            $userRuangan = $user->ruangan;
        }

        return view('usulan-pembelian.create', compact('ruangans', 'userRuangan', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Tentukan ruangan_id: admin pilih dari form, user biasa dari profil
        $ruanganId = ($user->hakAkses == '1')
            ? $request->ruangan_id
            : $user->ruangan_id;

        $request->validate([
            'tanggal_pengajuan'    => 'required|date',
            'keterangan'           => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.keterangan'   => 'required|string',
            'items.*.jumlah'       => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|integer|min:0',
            'lampirans'            => 'nullable|array|max:5',
            'lampirans.*'          => 'image|max:5120',
            'item_lampirans'       => 'nullable|array',
            'item_lampirans.*'     => 'array|max:3',
            'item_lampirans.*.*'   => 'image|max:5120',
        ]);

        if (!$ruanganId) {
            return redirect()->back()
                ->with('error', 'Anda belum memiliki ruangan. Hubungi admin untuk mengatur ruangan akun Anda.')
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user, $ruanganId) {
                $usulan = UsulanPembelian::create([
                    'nomor_usulan'          => UsulanPembelian::generateNomor(),
                    'tanggal_pengajuan'     => $request->tanggal_pengajuan,
                    'ruangan_id'            => $ruanganId,
                    'nama_penanggung_jawab' => $user->nama,
                    'keterangan'            => $request->keterangan,
                    'status'                => 'draft',
                    'dibuat_oleh'           => $user->idUser,
                ]);

                foreach ($request->items as $i => $item) {
                    if (!empty($item['keterangan'])) {
                        $detail = DetailUsulanPembelian::create([
                            'usulan_pembelian_id' => $usulan->id,
                            'no_urut'             => $i + 1,
                            'keterangan'          => $item['keterangan'],
                            'jumlah'              => $item['jumlah'],
                            'harga_satuan'        => $item['harga_satuan'] ?? 0,
                        ]);

                        // Lampiran per item
                        if ($request->hasFile("item_lampirans.$i")) {
                            foreach ($request->file("item_lampirans.$i") as $file) {
                                $path = $file->store('usulan-lampiran', 'public');
                                LampiranUsulanPembelian::create([
                                    'usulan_pembelian_id'         => $usulan->id,
                                    'detail_usulan_pembelian_id'  => $detail->id,
                                    'nama_file'                   => $file->getClientOriginalName(),
                                    'path'                        => $path,
                                    'mime_type'                   => $file->getMimeType(),
                                    'ukuran'                      => $file->getSize(),
                                ]);
                            }
                        }
                    }
                }

                // Lampiran umum (per usulan)
                if ($request->hasFile('lampirans')) {
                    foreach ($request->file('lampirans') as $file) {
                        $path = $file->store('usulan-lampiran', 'public');
                        LampiranUsulanPembelian::create([
                            'usulan_pembelian_id' => $usulan->id,
                            'nama_file'           => $file->getClientOriginalName(),
                            'path'                => $path,
                            'mime_type'           => $file->getMimeType(),
                            'ukuran'              => $file->getSize(),
                        ]);
                    }
                }
            });

            return redirect()->route('usulan.index')->with('success', 'Usulan pembelian berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $usulan = UsulanPembelian::with(['ruangan', 'pembuat', 'details.lampirans', 'approvals.approver', 'lampirans' => fn($q) => $q->umum()])->findOrFail($id);
        $user = Auth::user();

        $canApprove = $this->canUserApprove($user, $usulan);
        $canEdit = $usulan->status === 'draft' && $usulan->dibuat_oleh == $user->idUser;
        $canSubmit = $usulan->status === 'draft' && $usulan->dibuat_oleh == $user->idUser;

        return view('usulan-pembelian.show', compact('usulan', 'canApprove', 'canEdit', 'canSubmit'));
    }

    public function edit($id)
    {
        $usulan = UsulanPembelian::with(['details.lampirans'])->findOrFail($id);
        $user = Auth::user();

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != $user->idUser) {
            return redirect()->route('usulan.show', $id)->with('error', 'Usulan ini tidak dapat diedit.');
        }

        // Admin bisa pilih ruangan, user biasa ruangan sudah terkunci
        $ruangans = ($user->hakAkses == '1') ? Ruangan::where('is_active', 1)->get() : null;

        return view('usulan-pembelian.edit', compact('usulan', 'ruangans', 'user'));
    }

    public function update(Request $request, $id)
    {
        $usulan = UsulanPembelian::findOrFail($id);
        $user = Auth::user();

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != $user->idUser) {
            return redirect()->route('usulan.show', $id)->with('error', 'Usulan ini tidak dapat diedit.');
        }

        $request->validate([
            'tanggal_pengajuan'     => 'required|date',
            'keterangan'            => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.keterangan'    => 'required|string',
            'items.*.jumlah'        => 'required|integer|min:1',
            'items.*.harga_satuan'  => 'required|integer|min:0',
            'lampirans'             => 'nullable|array|max:5',
            'lampirans.*'           => 'image|max:5120',
            'item_lampirans'        => 'nullable|array',
            'item_lampirans.*'      => 'array|max:3',
            'item_lampirans.*.*'    => 'image|max:5120',
        ]);

        // Admin bisa ubah ruangan, user biasa tidak
        $ruanganId = ($user->hakAkses == '1' && $request->ruangan_id)
            ? $request->ruangan_id
            : $usulan->ruangan_id;

        try {
            DB::transaction(function () use ($request, $usulan, $ruanganId) {
                $usulan->update([
                    'tanggal_pengajuan' => $request->tanggal_pengajuan,
                    'ruangan_id'        => $ruanganId,
                    'keterangan'        => $request->keterangan,
                ]);

                // Update-in-place: pertahankan ID detail agar lampiran per item tidak hilang
                $submittedIds = collect($request->items)
                    ->pluck('id')->filter()->map('intval')->values()->toArray();

                // Hapus detail yang dihapus user beserta lampirannya
                $usulan->details()->whereNotIn('id', $submittedIds)->each(function ($d) {
                    foreach ($d->lampirans as $l) {
                        Storage::disk('public')->delete($l->path);
                    }
                    $d->lampirans()->delete();
                    $d->delete();
                });

                $urut = 1;
                foreach ($request->items as $i => $item) {
                    if (empty($item['keterangan'])) continue;

                    $detailId = !empty($item['id']) ? (int)$item['id'] : null;

                    if ($detailId) {
                        $detail = DetailUsulanPembelian::find($detailId);
                        if ($detail) {
                            $detail->update([
                                'no_urut'     => $urut,
                                'keterangan'  => $item['keterangan'],
                                'jumlah'      => $item['jumlah'],
                                'harga_satuan' => $item['harga_satuan'] ?? 0,
                            ]);
                        }
                    } else {
                        $detail = DetailUsulanPembelian::create([
                            'usulan_pembelian_id' => $usulan->id,
                            'no_urut'             => $urut,
                            'keterangan'          => $item['keterangan'],
                            'jumlah'              => $item['jumlah'],
                            'harga_satuan'        => $item['harga_satuan'] ?? 0,
                        ]);
                    }

                    // Lampiran per item (key = index i)
                    if ($detail && $request->hasFile("item_lampirans.$i")) {
                        foreach ($request->file("item_lampirans.$i") as $file) {
                            $path = $file->store('usulan-lampiran', 'public');
                            LampiranUsulanPembelian::create([
                                'usulan_pembelian_id'        => $usulan->id,
                                'detail_usulan_pembelian_id' => $detail->id,
                                'nama_file'                  => $file->getClientOriginalName(),
                                'path'                       => $path,
                                'mime_type'                  => $file->getMimeType(),
                                'ukuran'                     => $file->getSize(),
                            ]);
                        }
                    }

                    $urut++;
                }

                // Lampiran umum (per usulan)
                if ($request->hasFile('lampirans')) {
                    foreach ($request->file('lampirans') as $file) {
                        $path = $file->store('usulan-lampiran', 'public');
                        LampiranUsulanPembelian::create([
                            'usulan_pembelian_id' => $usulan->id,
                            'nama_file'           => $file->getClientOriginalName(),
                            'path'                => $path,
                            'mime_type'           => $file->getMimeType(),
                            'ukuran'              => $file->getSize(),
                        ]);
                    }
                }
            });

            return redirect()->route('usulan.show', $usulan->id)->with('success', 'Usulan berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $usulan = UsulanPembelian::findOrFail($id);

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != Auth::user()->idUser) {
            return redirect()->back()->with('error', 'Hanya usulan berstatus draft yang dapat dihapus.');
        }

        $usulan->delete();
        return redirect()->route('usulan.index')->with('success', 'Usulan berhasil dihapus.');
    }

    public function submit($id)
    {
        $usulan = UsulanPembelian::findOrFail($id);

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != Auth::user()->idUser) {
            return redirect()->back()->with('error', 'Usulan tidak dapat diajukan.');
        }

        if ($usulan->details()->count() === 0) {
            return redirect()->back()->with('error', 'Tambahkan minimal 1 item sebelum mengajukan.');
        }

        $usulan->update(['status' => 'diajukan']);
        return redirect()->route('usulan.index')->with('success', 'Usulan berhasil diajukan! Menunggu pemeriksaan.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:500']);

        $usulan = UsulanPembelian::with('approvals')->findOrFail($id);
        $user = Auth::user();

        if (!$this->canUserApprove($user, $usulan)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang menyetujui usulan ini saat ini.');
        }

        try {
            DB::transaction(function () use ($request, $usulan, $user) {
                $now = now()->toDateTimeString();
                $token = ApprovalUsulanPembelian::generateToken(
                    $usulan->id,
                    $user->idUser,
                    $now
                );

                $level = $user->approval_level ?? $this->getAdminApprovalLevel($usulan);

                ApprovalUsulanPembelian::create([
                    'usulan_pembelian_id' => $usulan->id,
                    'level'              => $level,
                    'user_id'            => $user->idUser,
                    'status'             => 'approved',
                    'catatan'            => $request->catatan,
                    'token'              => $token,
                    'approved_at'        => $now,
                ]);

                $nextStatus = $usulan->getNextStatus();
                $usulan->update(['status' => $nextStatus]);
            });

            return redirect()->route('usulan.show', $id)->with('success', 'Usulan berhasil disetujui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|max:500']);

        $usulan = UsulanPembelian::with('approvals')->findOrFail($id);
        $user = Auth::user();

        if (!$this->canUserApprove($user, $usulan)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang menolak usulan ini.');
        }

        try {
            DB::transaction(function () use ($request, $usulan, $user) {
                $now = now()->toDateTimeString();
                $level = $user->approval_level ?? $this->getAdminApprovalLevel($usulan);

                ApprovalUsulanPembelian::create([
                    'usulan_pembelian_id' => $usulan->id,
                    'level'              => $level,
                    'user_id'            => $user->idUser,
                    'status'             => 'rejected',
                    'catatan'            => $request->catatan,
                    'token'              => hash('sha256', uniqid('reject', true)),
                    'approved_at'        => $now,
                ]);

                $usulan->update(['status' => 'ditolak']);
            });

            return redirect()->route('usulan.show', $id)->with('warning', 'Usulan ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectItem(Request $request, $id, $detailId)
    {
        $request->validate(['alasan_tolak' => 'required|string|max:500']);

        $usulan = UsulanPembelian::findOrFail($id);
        $user = Auth::user();

        if (!$this->canUserApprove($user, $usulan)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang menolak item ini.');
        }

        $detail = DetailUsulanPembelian::where('id', $detailId)
            ->where('usulan_pembelian_id', $id)
            ->firstOrFail();

        $detail->update([
            'is_ditolak'  => true,
            'alasan_tolak' => $request->alasan_tolak,
        ]);

        return redirect()->route('usulan.show', $id)->with('warning', 'Item "' . $detail->keterangan . '" ditolak.');
    }

    public function restoreItem($id, $detailId)
    {
        $usulan = UsulanPembelian::findOrFail($id);
        $user = Auth::user();

        if (!$this->canUserApprove($user, $usulan)) {
            return redirect()->back()->with('error', 'Anda tidak berwenang mengubah item ini.');
        }

        $detail = DetailUsulanPembelian::where('id', $detailId)
            ->where('usulan_pembelian_id', $id)
            ->firstOrFail();

        $detail->update(['is_ditolak' => false, 'alasan_tolak' => null]);

        return redirect()->route('usulan.show', $id)->with('success', 'Item "' . $detail->keterangan . '" dipulihkan.');
    }

    public function resubmit($id)
    {
        $usulan = UsulanPembelian::findOrFail($id);

        if ($usulan->status !== 'ditolak' || $usulan->dibuat_oleh != Auth::user()->idUser) {
            return redirect()->back()->with('error', 'Tidak dapat mengajukan ulang usulan ini.');
        }

        DB::transaction(function () use ($usulan) {
            $usulan->approvals()->delete();
            $usulan->update(['status' => 'draft']);
        });

        return redirect()->route('usulan.edit', $id)->with('info', 'Silakan edit dan ajukan kembali usulan.');
    }

    public function uploadLampiran(Request $request, $id)
    {
        $usulan = UsulanPembelian::findOrFail($id);
        $user = Auth::user();

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != $user->idUser) {
            return redirect()->back()->with('error', 'Lampiran hanya dapat ditambahkan pada usulan berstatus draft milik Anda.');
        }

        $request->validate([
            'lampirans'   => 'required|array|max:5',
            'lampirans.*' => 'image|max:5120',
        ]);

        $detailId = $request->input('detail_id');
        // Validasi detail_id milik usulan ini
        if ($detailId) {
            $detailExists = DetailUsulanPembelian::where('id', $detailId)
                ->where('usulan_pembelian_id', $id)
                ->exists();
            if (!$detailExists) $detailId = null;
        }

        foreach ($request->file('lampirans') as $file) {
            $path = $file->store('usulan-lampiran', 'public');
            LampiranUsulanPembelian::create([
                'usulan_pembelian_id'        => $usulan->id,
                'detail_usulan_pembelian_id' => $detailId ?: null,
                'nama_file'                  => $file->getClientOriginalName(),
                'path'                       => $path,
                'mime_type'                  => $file->getMimeType(),
                'ukuran'                     => $file->getSize(),
            ]);
        }

        return redirect()->route('usulan.show', $id)->with('success', 'Lampiran berhasil diunggah.');
    }

    public function deleteLampiran($id, $lampiranId)
    {
        $usulan = UsulanPembelian::findOrFail($id);
        $user = Auth::user();

        if ($usulan->status !== 'draft' || $usulan->dibuat_oleh != $user->idUser) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus lampiran.');
        }

        $lampiran = LampiranUsulanPembelian::where('id', $lampiranId)
            ->where('usulan_pembelian_id', $id)
            ->firstOrFail();

        Storage::disk('public')->delete($lampiran->path);
        $lampiran->delete();

        return redirect()->route('usulan.show', $id)->with('success', 'Lampiran berhasil dihapus.');
    }

    public function pdf($id)
    {
        $usulan = UsulanPembelian::with(['ruangan', 'pembuat', 'details.lampirans', 'approvals.approver', 'lampirans' => fn($q) => $q->umum()])->findOrFail($id);

        // Siapkan data approval per level (1-4)
        $approvalData = [];
        for ($level = 1; $level <= 4; $level++) {
            $approvalData[$level] = $usulan->getApprovalByLevel($level);
        }

        // Generate QR code image untuk tiap approval yang sudah approved
        $qrImages = [];
        foreach ($approvalData as $level => $approval) {
            if ($approval) {
                $verifyUrl = route('verify', $approval->token);
                $qrImages[$level] = $this->generateQrBase64($verifyUrl);
            }
        }

        // QR untuk pembuat (link ke halaman detail usulan)
        $qrPembuat = $this->generateQrBase64(route('usulan.show', $usulan->id));

        // Siapkan detail items (pastikan 10 baris)
        $details = $usulan->details->toArray();
        while (count($details) < 10) {
            $details[] = null;
        }

        // Konversi lampiran ke base64 untuk embed di PDF
        // Lampiran umum (per usulan)
        $lampiranBase64 = [];
        foreach ($usulan->lampirans as $lampiran) {
            $fullPath = Storage::disk('public')->path($lampiran->path);
            if (file_exists($fullPath)) {
                $lampiranBase64[] = [
                    'nama_file' => $lampiran->nama_file,
                    'mime_type' => $lampiran->mime_type ?? 'image/jpeg',
                    'base64'    => 'data:' . ($lampiran->mime_type ?? 'image/jpeg') . ';base64,' . base64_encode(file_get_contents($fullPath)),
                ];
            }
        }

        // Lampiran per item: [ detail_id => ['keterangan'=>..., 'fotos'=>[...]] ]
        $lampiranPerItem = [];
        foreach ($usulan->details as $detail) {
            if ($detail->lampirans->isEmpty()) continue;
            $fotos = [];
            foreach ($detail->lampirans as $lampiran) {
                $fullPath = Storage::disk('public')->path($lampiran->path);
                if (file_exists($fullPath)) {
                    $fotos[] = [
                        'nama_file' => $lampiran->nama_file,
                        'mime_type' => $lampiran->mime_type ?? 'image/jpeg',
                        'base64'    => 'data:' . ($lampiran->mime_type ?? 'image/jpeg') . ';base64,' . base64_encode(file_get_contents($fullPath)),
                    ];
                }
            }
            if (!empty($fotos)) {
                $lampiranPerItem[] = [
                    'no_urut'    => $detail->no_urut,
                    'keterangan' => $detail->keterangan,
                    'fotos'      => $fotos,
                ];
            }
        }

        $pdf = \PDF::loadView('usulan-pembelian.pdf', compact('usulan', 'approvalData', 'qrImages', 'qrPembuat', 'details', 'lampiranBase64', 'lampiranPerItem'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('usulan-pembelian-' . $usulan->nomor_usulan . '.pdf');
    }

    public function waLink($id)
    {
        $usulan = UsulanPembelian::with('ruangan')->findOrFail($id);

        $pdfUrl  = route('usulan.pdf', $id);
        $total   = number_format($usulan->total, 0, ',', '.');
        $message = "📋 *USULAN PEMBELIAN BARANG*\n\n"
            . "Nomor   : {$usulan->nomor_usulan}\n"
            . "Tanggal : " . $usulan->tanggal_pengajuan->format('d/m/Y') . "\n"
            . "Unit    : {$usulan->ruangan->nama_ruangan}\n"
            . "PJ      : {$usulan->nama_penanggung_jawab}\n"
            . "Total   : Rp {$total}\n"
            . "Status  : {$usulan->status_label}\n\n"
            . "🔗 Download PDF:\n{$pdfUrl}";

        $waNumber = \DB::table('settings')->where('key', 'wa_direktur')->value('value') ?? '';
        $waUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $waNumber) . '?text=' . urlencode($message);

        return redirect($waUrl);
    }

    // --- Private Helpers ---

    private function canUserApprove(User $user, UsulanPembelian $usulan): bool
    {
        $requiredLevel = $usulan->getRequiredApprovalLevel();

        if ($requiredLevel === null) return false;

        // Pembuat tidak bisa approve usulannya sendiri
        if ($usulan->dibuat_oleh == $user->idUser) return false;

        // Admin bisa approve semua level (kecuali usulannya sendiri, sudah dicek di atas)
        if ($user->hakAkses == '1') {
            return true;
        }

        if ($user->approval_level != $requiredLevel) return false;

        // Level 1: hanya approver yang ditunjuk untuk ruangan ini
        if ($requiredLevel === 1) {
            $approverId = $usulan->ruangan->approver_id ?? null;
            // Jika ruangan belum assign approver, semua level-1 boleh approve
            if ($approverId && $approverId !== $user->idUser) return false;
        }

        return true;
    }

    private function getAdminApprovalLevel(UsulanPembelian $usulan): int
    {
        return $usulan->getRequiredApprovalLevel() ?? 1;
    }

    private function getStatusRequiringLevel(int $level): ?string
    {
        $map = [
            1 => 'diajukan',     // Kabag/Kabid periksa
            2 => 'diperiksa',    // Direktur konfirmasi
            3 => 'dikonfirmasi', // Ka. Keuangan
            4 => 'diketahui',    // Bendahara acc
        ];
        return $map[$level] ?? null;
    }

    private function generateQrBase64(string $text): string
    {
        // Menggunakan milon/barcode untuk generate QR code sebagai base64
        $generator = new \Milon\Barcode\DNS2D();
        return 'data:image/png;base64,' . $generator->getBarcodePNG($text, 'QRCODE', 3, 3);
    }
}
