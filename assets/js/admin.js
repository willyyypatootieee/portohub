(function () {
    'use strict';

    /*
     * Superadmin — editor pengguna sebaris.
     *
     * Tombol "Edit" pada baris pengguna membuka baris form di bawahnya;
     * hanya satu editor yang terbuka pada satu waktu.
     */
    var openId = null;

    function rowFor(id) {
        return document.querySelector('[data-admin-edit-row="' + id + '"]');
    }

    function close(id) {
        var row = rowFor(id);
        if (row) {
            row.hidden = true;
        }
        if (openId === id) {
            openId = null;
        }
    }

    document.querySelectorAll('[data-admin-edit]').forEach(function (button) {
        button.addEventListener('click', function () {
            var id = button.getAttribute('data-admin-edit');
            var row = rowFor(id);

            if (!row) {
                return;
            }

            if (openId !== null && openId !== id) {
                close(openId);
            }

            row.hidden = !row.hidden;
            openId = row.hidden ? null : id;

            if (!row.hidden) {
                var field = row.querySelector('input[name="display_name"]');
                if (field) {
                    field.focus();
                }
            }
        });
    });

    document.querySelectorAll('[data-admin-edit-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            close(button.getAttribute('data-admin-edit-close'));
        });
    });
}());
