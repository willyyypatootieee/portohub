(function () {
    'use strict';

    /* Sidebar seluler: tombol hamburger. */
    var toggle = document.querySelector('.sidebar-toggle');
    var sidebar = document.querySelector('.studio-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () { sidebar.classList.toggle('is-open'); });
    }

    /* Pratinjau username pada form profil. */
    var username = document.querySelector('input[name="username"]');
    var preview = document.querySelector('[data-username-preview]');
    if (username && preview) {
        username.addEventListener('input', function () {
            preview.textContent = username.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        });
    }

    /*
     * Editor proyek dilipat secara default. Tombol "New project" dan tautan
     * "Cancel" membuka/menutupnya, sehingga halaman daftar proyek tidak
     * dipenuhi form raksasa saat creator hanya ingin melihat daftar.
     */
    var editor = document.getElementById('project-form');
    var openers = document.querySelectorAll('[data-project-toggle]');
    var closers = document.querySelectorAll('[data-project-close]');

    if (editor) {
        var setOpen = function (open) {
            editor.classList.toggle('is-collapsed', !open);
            openers.forEach(function (el) { el.setAttribute('aria-expanded', open ? 'true' : 'false'); });

            if (open) {
                editor.scrollIntoView({ behavior: 'smooth', block: 'start' });
                var field = editor.querySelector('input[name="title"]');
                if (field) { field.focus(); }
            }
        };

        openers.forEach(function (el) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                setOpen(editor.classList.contains('is-collapsed'));
            });
        });

        closers.forEach(function (el) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                setOpen(false);
                history.replaceState(null, '', location.pathname + location.search);
            });
        });
    }
}());
