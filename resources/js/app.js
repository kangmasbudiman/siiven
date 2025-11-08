require('./bootstrap');


import Swal from 'sweetalert2';

// Make Swal available globally
window.Swal = Swal;

// SweetAlert mixin untuk reusable functions
window.showAlert = function (icon, title, text, timer = 3000) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        timer: timer,
        showConfirmButton: false,
        timerProgressBar: true
    });
};

window.showConfirm = function (title, text, confirmButtonText = 'Ya', cancelButtonText = 'Batal') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText
    });
};