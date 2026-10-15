(function () {
    'use strict';

    /*
     * Modal bersama untuk creator studio.
     *
     * Tombol [data-modal-open="<id>"] membuka elemen #<id>; [data-modal-close]
     * di dalamnya menutupnya. Tombol Esc dan klik latar juga menutup. Fokus
     * dipindahkan ke field pertama agar pengguna bisa langsung mengetik.
     */

    var openModal = null;
    var lastFocused = null;

    function show(modal) {
        if (!modal) {
            return;
        }

        modal.hidden = false;
        document.body.classList.add('has-modal');
        openModal = modal;
        lastFocused = document.activeElement;

        var field = modal.querySelector('input:not([type="hidden"]), textarea, select');
        if (field) {
            field.focus();
        }
    }

    function hide(modal) {
        if (!modal) {
            return;
        }

        modal.hidden = true;

        if (openModal === modal) {
            openModal = null;
        }

        if (!document.querySelector('.modal:not([hidden])')) {
            document.body.classList.remove('has-modal');
        }

        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    document.querySelectorAll('[data-modal-open]').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            show(document.getElementById(trigger.getAttribute('data-modal-open')));
        });
    });

    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                hide(modal);
            }
        });

        modal.querySelectorAll('[data-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                hide(modal);
            });
        });

        // Kirim form di dalam modal lewat tombol di footer.
        modal.querySelectorAll('[data-modal-submit]').forEach(function (button) {
            button.addEventListener('click', function () {
                var form = modal.querySelector('form');
                if (form) {
                    form.requestSubmit();
                }
            });
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && openModal) {
            hide(openModal);
        }
    });

    /*
     * Buka otomatis saat halaman memuat dengan tanda tertentu:
     * ?edit=<id> memerlukan modal proyek, error validasi membuka modal yang
     * relevan supaya pesan kesalahan langsung terlihat.
     */
    document.querySelectorAll('.modal[data-modal-auto]').forEach(function (modal) {
        show(modal);
    });
}());
