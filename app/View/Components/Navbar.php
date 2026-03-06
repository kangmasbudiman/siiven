<?php

namespace App\View\Components;

use Illuminate\View\Component;

use App\Repositories\StuffRepository;
use App\Models\UsulanPembelian;

class Navbar extends Component
{

    public $limit = 0;
    public $pendingApproval = 0;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(StuffRepository $stuffRepo)
    {
        $this->limit = $stuffRepo->countLimit();

        $user = auth()->user();
        if ($user && $user->approval_level) {
            $statusMap = [1 => 'diajukan', 2 => 'diperiksa', 3 => 'dikonfirmasi', 4 => 'diketahui'];
            $status = $statusMap[$user->approval_level] ?? null;
            if ($status) {
                $query = UsulanPembelian::where('status', $status);

                // Level 1: filter hanya ruangan yang ditunjuk untuk user ini
                if ($user->approval_level == 1) {
                    $query->whereHas('ruangan', function ($q) use ($user) {
                        $q->where(function ($q2) use ($user) {
                            $q2->where('approver_id', $user->idUser)
                               ->orWhereNull('approver_id'); // ruangan tanpa approver tetap muncul
                        });
                    });
                }

                $this->pendingApproval = $query->count();
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.navbar');
    }
}
