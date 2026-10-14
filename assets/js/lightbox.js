/* =============================================================================
   lightbox.js
   Pratinjau gambar/GIF dalam overlay. Dipakai halaman Projects (studio) dan
   portfolio publik. Halaman tanpa elemen [data-lightbox-src] dibiarkan kosong.
   ============================================================================= */

(function () {
    'use strict';

    var triggers = document.querySelectorAll('[data-lightbox-src]');
    if (triggers.length === 0) {
        return;
    }

    var overlay = document.createElement('div');
    overlay.className = 'media-lightbox';
    overlay.hidden = true;
    overlay.innerHTML =
        '<div class="media-lightbox-backdrop" data-lightbox-close></div>' +
        '<figure class="media-lightbox-figure">' +
        '   <img class="media-lightbox-image" alt="">' +
        '   <figcaption class="media-lightbox-caption"></figcaption>' +
        '</figure>' +
        '<button class="media-lightbox-close" type="button" aria-label="Tutup pratinjau" data-lightbox-close>&times;</button>';
    document.body.appendChild(overlay);

    var image = overlay.querySelector('.media-lightbox-image');
    var caption = overlay.querySelector('.media-lightbox-caption');
    var closers = overlay.querySelectorAll('[data-lightbox-close]');
    var lastFocusedElement = null;

    function close() {
        if (overlay.hidden) {
            return;
        }
        overlay.hidden = true;
        document.documentElement.classList.remove('has-lightbox');
        image.removeAttribute('src');

        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        }
    }

    function open(trigger) {
        var src = trigger.getAttribute('data-lightbox-src');
        if (!src) {
            return;
        }

        lastFocusedElement = document.activeElement;
        image.src = src;
        image.alt = trigger.getAttribute('data-lightbox-label') || '';
        caption.textContent = trigger.getAttribute('data-lightbox-label') || '';
        overlay.hidden = false;
        document.documentElement.classList.add('has-lightbox');

        var closeButton = overlay.querySelector('.media-lightbox-close');
        if (closeButton) {
            closeButton.focus();
        }
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            open(trigger);
        });
    });

    closers.forEach(function (closer) {
        closer.addEventListener('click', close);
    });

    document.addEventListener('keydown', function (event) {
        if (overlay.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        }

        // Tahan fokus di dalam overlay.
        if (event.key === 'Tab') {
            event.preventDefault();
            var closeButton = overlay.querySelector('.media-lightbox-close');
            if (closeButton) {
                closeButton.focus();
            }
        }
    });
}());
