<livewire:scripts />

<script src="{{ asset('sufee-admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('sufee-admin/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('sufee-admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('sufee-admin/assets/js/main.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const appSelect = document.getElementById('app_id');
        const rateInput = document.getElementById('rate');
        const coinType = document.getElementById('coin_type');
        const amountDue = document.getElementById('amount_due');
        const coinQty = document.getElementById('coin_qty');
        const paymentType = document.getElementById('payment_type');
        const amountPaid = document.getElementById('amount_paid');
        const outstanding = document.getElementById('outstanding_amount');
        const method = document.getElementById('payment_method');
        const account = document.getElementById('payment_account_id');
        const phone = document.getElementById('customer_phone');
        const custType = document.getElementById('customer_type');
        const reseller = document.getElementById('reseller_id');
        const editReseller = document.getElementById('edit_reseller_id');
        const notesTransaction = document.getElementById('notes_transaction');
        const appqty = document.getElementById('appqty');
     

        // Auto isi rate & coin type
        appSelect.addEventListener('change', () => {
            const opt = appSelect.selectedOptions[0];
            rateInput.value = opt.dataset.rate || '';
            coinType.value = opt.dataset.coinType || '';
            appqty.value = opt.dataset.appqty || '';
            calculateCoin();
        });

        // Hitung coin_qty otomatis
        function calculateCoin() {
            const rate = parseFloat(rateInput.value) || 0;
            const amt = parseFloat(amountDue.value) || 0;
            coinQty.value = rate > 0 ? (amt / rate).toFixed(2) : 0;
            calcOutstanding();
        }

        // Hitung outstanding otomatis
        function calcOutstanding() {
            const due = parseFloat(amountDue.value) || 0;
            const paid = parseFloat(amountPaid.value) || 0;
            outstanding.value = (due - paid).toFixed(2);
        }

        amountDue.addEventListener('input', calculateCoin);
        amountPaid.addEventListener('input', calcOutstanding);

        // Logika Payment Type
        paymentType.addEventListener('change', () => {
            const type = paymentType.value;
            if (type === 'Lunas') {
                amountPaid.disabled = false;
                method.disabled = false;
                account.disabled = false;
                phone.disabled = true;
                phone.value = '';
            } else {
                amountPaid.disabled = false;
                method.disabled = true;
                account.disabled = true;
                phone.disabled = false;
            }
        });

        // Logika Customer Type
        custType.addEventListener('change', () => {
            const type = custType.value;
            if (type === 'Reseller') {
                reseller.disabled = false;
            } else {
                reseller.disabled = true;
                reseller.value = '';
            }
        });
    });
</script>



<script>
document.addEventListener("DOMContentLoaded", function() {

    const reloadButtons = document.querySelectorAll('.btn-reload');

    function safeSet(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }

    reloadButtons.forEach(btn => {
        btn.addEventListener('click', () => {

            console.log("Reload Clicked →", btn.dataset.appId, btn.dataset.appName);

            safeSet('reload_app_id', btn.dataset.appId);
            safeSet('reload_app_name', btn.dataset.appName);

            const reloadModal = new bootstrap.Modal(document.getElementById('reloadModal'));
            reloadModal.show();
        });
    });

});
</script>





<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.btn-edit-transaction');

        function safeSet(id, value) {
            const el = document.getElementById(id);
            if (el) el.value = value || '';
        }

        editButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                console.log('Edit clicked:', btn.dataset.paymentAccountId);

                safeSet('edit_id', btn.dataset.id);
                safeSet('edit_app_id', btn.dataset.appId);
                safeSet('edit_coin_type', btn.dataset.coinType);
                safeSet('edit_rate', btn.dataset.rate);
                safeSet('edit_amount_due', btn.dataset.amountDue);
                safeSet('edit_amount_paid', btn.dataset.amountPaid);
                safeSet('edit_outstanding', btn.dataset.outstanding);
                safeSet('edit_payment_type', btn.dataset.paymentType);
                safeSet('edit_payment_method', btn.dataset.paymentMethod);
                safeSet('edit_payment_account_id', btn.dataset.paymentAccountId);
                safeSet('edit_customer_type', btn.dataset.customerType);
                safeSet('edit_customer_phone', btn.dataset.customerPhone);
                safeSet('edit_reseller_id', btn.dataset.resellerId);
                safeSet('edit_notes_transaction', btn.dataset.notesTransaction || '');
                safeSet('edit_coin_qty', btn.dataset.coinQty || (btn.dataset.amountDue / btn
                    .dataset.rate).toFixed(2));


                // ----------- MODAL EDIT ----------
                const editAppSelect = document.getElementById('edit_app_id');
                const editRateInput = document.getElementById('edit_rate');
                const editCoinType = document.getElementById('edit_coin_type');
                const editAmountDue = document.getElementById('edit_amount_due');
                const editCoinQty = document.getElementById('edit_coin_qty');

                if (editAppSelect) {
                    editAppSelect.addEventListener('change', function() {
                        const opt = editAppSelect.selectedOptions[0];
                        editRateInput.value = opt?.dataset.rate || '';
                        editCoinType.value = opt?.dataset.coinType || '';
                        recalcCoinQtyEdit();
                    });
                }

                // Hitung ulang coin qty otomatis
                function recalcCoinQtyEdit() {
                    const rate = parseFloat(editRateInput.value) || 0;
                    const amount = parseFloat(editAmountDue.value) || 0;
                    editCoinQty.value = rate > 0 ? (amount / rate).toFixed(2) : 0;
                }

                if (editAmountDue) {
                    editAmountDue.addEventListener('input', recalcCoinQtyEdit);
                }

                // ---- Bagian Customer Type + Reseller ----
                const editCustomerType = document.getElementById('edit_customer_type');
                const editResellerSelect = document.getElementById('edit_reseller_id');
                const customerType = btn.dataset.customerType;
                const resellerId = btn.dataset.resellerId;


                if (editCustomerType && editResellerSelect) {
                    editCustomerType.value = customerType || 'Non-Reseller';

                    if (customerType === "Reseller") {
                        editResellerSelect.disabled = false;
                        editResellerSelect.required = true;
                        editResellerSelect.value = resellerId || '';
                    } else {
                        editResellerSelect.disabled = true;
                        editResellerSelect.required = false;
                        editResellerSelect.value = '';
                    }
                }

                // ====== LOGIKA UNTUK MODAL EDIT ======
                const editCustType = document.getElementById('edit_customer_type');
                const editReseller = document.getElementById('edit_reseller_id');

                if (editCustType && editReseller) {
                    editCustType.addEventListener('change', function() {
                        if (this.value === 'Reseller') {
                            editReseller.disabled = false;
                            editReseller.required = true;
                        } else {
                            editReseller.disabled = true;
                            editReseller.required = false;
                            editReseller.value = '';
                        }
                    });
                }

                // Optional: buka modal kalau belum otomatis
                const editModal = new bootstrap.Modal(document.getElementById(
                    'modalEditTransaction'));
                editModal.show();



            });
        });
    });
</script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        editModal.hide();
        // Fungsi untuk membersihkan semua efek modal setelah ditutup
        function cleanupModals() {
            // Hapus semua backdrop yang masih tertinggal
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // Hapus class dan style yang bikin body tidak bisa discroll
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        // Dengarkan setiap kali ada modal Bootstrap yang ditutup
            window.addEventListener('hidden.bs.modal', function(event) {
            console.log(`Modal ditutup: #${event.target.id}`);
            cleanupModals();
        });

        // Tambahan: kalau ada error modal tidak tertutup sempurna (misal karena reload AJAX)
        // jalankan cleanup saat halaman fokus kembali
        window.addEventListener('focus', cleanupModals);
    });
</script>






<script>
document.addEventListener("DOMContentLoaded", function() {
    // Tangkap semua tombol hapus
    const deleteButtons = document.querySelectorAll('.btn-delete-transaction');
    const formDelete = document.getElementById('formDeleteTransaction');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trxId = this.dataset.id;
            if (trxId && formDelete) {
                // Ubah action form delete sesuai ID transaksi
                formDelete.action = `{{ url('transaction') }}/${trxId}/destroy`;
               
            }
        });
    });
});
</script>






 <script>
document.addEventListener("DOMContentLoaded", function() {
    const approveButtons = document.querySelectorAll('.btn-approve-transaction');
    const formApprove = document.getElementById('formApproveTransaction');

    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const trxId = this.dataset.id;
            if (trxId && formApprove) {
                // Ubah action sesuai ID transaksi
                formApprove.action = `{{ url('transaction/approve') }}/${trxId}`;
                console.log(`✅ Set form approve action ke: /transaction/approve/${trxId}`);
            }
        });
    });
});
</script>

























@stack('js')
