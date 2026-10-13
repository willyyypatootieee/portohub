(function () {
    'use strict';
    var toggle = document.querySelector('.sidebar-toggle');
    var sidebar = document.querySelector('.studio-sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () { sidebar.classList.toggle('is-open'); });
    }
    var username = document.querySelector('input[name="username"]');
    var preview = document.querySelector('[data-username-preview]');
    if (username && preview) {
        username.addEventListener('input', function () { preview.textContent = username.value.toLowerCase().replace(/[^a-z0-9-]/g, ''); });
    }
}());
