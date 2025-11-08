<!-- Modal Create Bank -->
<div class="modal fade" id="createBankModal" tabindex="-1" aria-labelledby="createBankModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bank.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createBankModalLabel">Tambah Bank Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" 
                               placeholder="Contoh: BCA, Mandiri, BNI" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" 
                               placeholder="Contoh: 1234567890" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" 
                               placeholder="Nama lengkap pemilik rekening" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="branch" class="form-label">Cabang Bank (Optional)</label>
                        <input type="text" class="form-control" id="branch" name="branch" 
                               placeholder="Contoh: Cabang Sudirman">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Keterangan (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Keterangan tambahan"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Bank -->
<div class="modal fade" id="editBankModal" tabindex="-1" aria-labelledby="editBankModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBankForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBankModalLabel">Edit Data Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_bank_name" class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_account_number" class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" id="edit_account_number" name="account_number" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_account_name" class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" id="edit_account_name" name="account_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_branch" class="form-label">Cabang Bank (Optional)</label>
                        <input type="text" class="form-control" id="edit_branch" name="branch">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Keterangan (Optional)</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>