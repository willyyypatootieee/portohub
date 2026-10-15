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
     * Konfirmasi sebelum menghapus media dari galeri: kartu media memakai
     * form biasa, jadi sebagian aksi sensitif dijaga lewat atribut onsubmit
     * di markup. Tidak ada lagi editor proyek yang perlu dilipat — form
     * sekarang hidup di dalam modal (lihat assets/js/modal.js).
     */
}());
