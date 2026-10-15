/**
 * Flipbook CV berbasis PDF.js.
 *
 * Setiap halaman PDF dirender ke <canvas> dan ditampilkan sebagai buku
 * dua halaman (spread kiri–kanan) dengan animasi balik 3D CSS
 * (teknik flip book: https://github.com/fchavonet/creative_coding-flip_book).
 *
 * Fitur:
 * - Spread dua halaman; halaman 1 sendirian di kanan seperti sampul buku.
 * - Navigasi tombol dan keyboard (panah kiri/kanan, Escape untuk tutup).
 * - Tombol unduh PDF dan tombol buka di tab baru (fullscreen).
 *
 * PDF.js dimuat dari CDN saat pertama kali dibutuhkan (lazy), jadi halaman
 * yang tidak membuka CV tidak membayar biaya unduhannya.
 */
(function () {
    'use strict';

    var PDFJS_VERSION = '4.6.82';
    var PDFJS_BASE = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VERSION + '/build/';
    var pdfjsPromise = null;
    var FLIP_MS = 420;

    /** Memuat PDF.js sekali saja, lalu memakai ulang promise-nya. */
    function loadPdfJs() {
        if (pdfjsPromise) {
            return pdfjsPromise;
        }

        pdfjsPromise = import(PDFJS_BASE + 'pdf.min.mjs').then(function (module) {
            module.GlobalWorkerOptions.workerSrc = PDFJS_BASE + 'pdf.worker.min.mjs';
            return module;
        });

        return pdfjsPromise;
    }

    function createModal() {
        var modal = document.createElement('div');
        modal.className = 'cv-flipbook-modal';
        modal.setAttribute('data-cv-modal', '');
        modal.hidden = true;
        modal.innerHTML =
            '<div class="cv-flipbook-dialog" role="dialog" aria-modal="true" aria-label="CV flipbook">' +
                '<div class="cv-flipbook-head">' +
                    '<strong data-cv-modal-title>CV</strong>' +
                    '<div class="cv-flipbook-actions">' +
                        '<button class="cv-flipbook-icon" type="button" data-cv-fullscreen aria-label="Buka di tab baru" title="Buka di tab baru">' +
                            '<svg viewBox="0 0 24 24"><path d="M14 4h6v6"/><path d="M20 4 13 11"/><path d="M10 20H4v-6"/><path d="m4 20 7-7"/></svg>' +
                        '</button>' +
                        '<a class="cv-flipbook-icon" data-cv-download aria-label="Unduh PDF" title="Unduh PDF" download>' +
                            '<svg viewBox="0 0 24 24"><path d="M12 4v11"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/></svg>' +
                        '</a>' +
                        '<button class="cv-flipbook-icon" type="button" data-cv-close aria-label="Tutup" title="Tutup">' +
                            '<svg viewBox="0 0 24 24"><path d="m6 6 12 12"/><path d="m18 6-12 12"/></svg>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<p class="cv-flipbook-status" data-cv-status hidden></p>' +
                '<div class="cv-stage">' +
                    '<button type="button" class="cv-flipbook-nav" data-cv-prev aria-label="Halaman sebelumnya">' +
                        '<svg viewBox="0 0 24 24"><path d="m14 6-6 6 6 6"/></svg>' +
                    '</button>' +
                    '<div class="cv-book" data-cv-book></div>' +
                    '<button type="button" class="cv-flipbook-nav" data-cv-next aria-label="Halaman berikutnya">' +
                        '<svg viewBox="0 0 24 24"><path d="m10 6 6 6-6 6"/></svg>' +
                    '</button>' +
                '</div>' +
                '<div class="cv-page-nav">' +
                    '<span class="cv-page-count" data-cv-count>—</span>' +
                '</div>' +
            '</div>';
        document.body.appendChild(modal);
        return modal;
    }

    /** Mengubah halaman PDF menjadi elemen <canvas>. */
    function renderPage(pdf, pageNumber, scale) {
        return pdf.getPage(pageNumber).then(function (page) {
            var viewport = page.getViewport({ scale: scale });
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');

            canvas.width = Math.floor(viewport.width);
            canvas.height = Math.floor(viewport.height);

            return page.render({ canvasContext: context, viewport: viewport }).promise.then(function () {
                return canvas;
            });
        });
    }

    function Flipbook(src, title) {
        this.src = src;
        this.title = title;
        this.pdf = null;
        this.pageCount = 0;
        this.current = 0; // indeks halaman kiri pada spread (atau halaman tunggal).
        this.scale = 1.1;
        this.busy = false;
        this.modal = createModal();
        this.book = this.modal.querySelector('[data-cv-book]');
        this.status = this.modal.querySelector('[data-cv-status]');
        this.count = this.modal.querySelector('[data-cv-count]');
        this.prevButton = this.modal.querySelector('[data-cv-prev]');
        this.nextButton = this.modal.querySelector('[data-cv-next]');
        this.fullscreenLink = this.modal.querySelector('[data-cv-fullscreen]');
        this.downloadLink = this.modal.querySelector('[data-cv-download]');
        this.modal.querySelector('[data-cv-modal-title]').textContent = title || 'CV';

        this.bindEvents();
    }

    /**
     * Halaman kiri untuk spread pada posisi `page`.
     * Halaman 1 tampil sendirian (seperti sampul), setelahnya berpasangan
     * genap–ganji: (2,3), (4,5), dst.
     */
    Flipbook.prototype.leftPageAt = function (page) {
        if (page === 0) {
            return 0;
        }

        return page % 2 === 1 ? page : page - 1;
    };

    Flipbook.prototype.bindEvents = function () {
        var self = this;

        this.modal.querySelector('[data-cv-close]').addEventListener('click', function () {
            self.close();
        });

        this.modal.addEventListener('click', function (event) {
            if (event.target === self.modal) {
                self.close();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (self.modal.hidden) {
                return;
            }

            if (event.key === 'Escape') {
                self.close();
            } else if (event.key === 'ArrowRight') {
                self.next();
            } else if (event.key === 'ArrowLeft') {
                self.previous();
            }
        });

        this.prevButton.addEventListener('click', function () {
            self.previous();
        });

        this.nextButton.addEventListener('click', function () {
            self.next();
        });
    };

    Flipbook.prototype.open = function () {
        var self = this;

        this.modal.hidden = false;
        document.body.style.overflow = 'hidden';

        if (this.pdf !== null) {
            this.showSpread();
            return;
        }

        this.setStatus('Memuat CV…', false);

        this.fullscreenLink.href = this.src;
        this.fullscreenLink.target = '_blank';
        this.fullscreenLink.rel = 'noopener';
        this.downloadLink.href = this.src + (this.src.indexOf('?') === -1 ? '?' : '&') + 'download=1';

        loadPdfJs()
            .then(function (pdfjs) {
                return pdfjs.getDocument(self.src).promise;
            })
            .then(function (pdf) {
                self.pdf = pdf;
                self.pageCount = pdf.numPages;
                self.setStatus('', true);
                self.showSpread();
            })
            .catch(function (error) {
                self.setStatus('CV tidak dapat dimuat: ' + error.message, false);
            });
    };

    /** Elemen wadah satu halaman buku. */
    function pageHolder() {
        var div = document.createElement('div');
        div.className = 'cv-page';
        return div;
    }

    /**
     * Menampilkan buku dua halaman pada posisi `current` (indeks halaman kiri).
     *
     * Halaman 1 tampil sendirian di kanan seperti sampul; setelah itu
     * berpasangan genap–ganji: (2,3), (4,5), dst.
     */
    Flipbook.prototype.showSpread = function () {
        var leftIndex = this.leftPageAt(this.current);
        var rightIndex = leftIndex === 0 ? (this.pageCount > 1 ? 1 : null) : leftIndex + 1;

        this.book.innerHTML = '';
        this.prevButton.disabled = this.current === 0;
        this.nextButton.disabled = rightIndex === null || rightIndex >= this.pageCount - 1;

        var last = rightIndex === null ? leftIndex : rightIndex;
        this.count.textContent = leftIndex === rightIndex
            ? 'Halaman ' + (leftIndex + 1) + ' / ' + this.pageCount
            : 'Halaman ' + (leftIndex + 1) + '–' + (last + 1) + ' / ' + this.pageCount;

        var leftSlot = pageHolder();
        var rightSlot = pageHolder();
        leftSlot.classList.add('is-left');
        rightSlot.classList.add('is-right');
        this.book.appendChild(leftSlot);
        this.book.appendChild(rightSlot);

        var self = this;
        var requests = [renderPage(this.pdf, leftIndex + 1, this.scale).then(function (canvas) {
            leftSlot.appendChild(canvas);
        })];

        if (rightIndex !== null) {
            requests.push(renderPage(this.pdf, rightIndex + 1, this.scale).then(function (canvas) {
                rightSlot.appendChild(canvas);
            }));
        }

        Promise.all(requests).catch(function () {
            self.setStatus('Sebagian halaman CV gagal dimuat.', false);
        });
    };

    /** Animasi flip singkat pada buku sebelum isi diganti. */
    Flipbook.prototype.animateFlip = function (direction) {
        var self = this;
        this.book.classList.add(direction === 'next' ? 'is-flipping-next' : 'is-flipping-prev');

        return new Promise(function (resolve) {
            window.setTimeout(function () {
                self.book.classList.remove('is-flipping-next', 'is-flipping-prev');
                resolve();
            }, FLIP_MS);
        });
    };

    Flipbook.prototype.next = function () {
        if (this.busy || this.nextButton.disabled) {
            return;
        }

        this.busy = true;
        var self = this;

        this.animateFlip('next').then(function () {
            self.current = self.current === 0 ? 1 : self.current + 2;
            self.showSpread();
            self.busy = false;
        });
    };

    Flipbook.prototype.previous = function () {
        if (this.busy || this.current === 0) {
            return;
        }

        this.busy = true;
        var self = this;

        this.animateFlip('prev').then(function () {
            var target = self.current - 2;
            self.current = target <= 0 ? 0 : target;
            self.showSpread();
            self.busy = false;
        });
    };

    Flipbook.prototype.close = function () {
        this.modal.hidden = true;
        document.body.style.overflow = '';
    };

    Flipbook.prototype.setStatus = function (message, hide) {
        this.status.textContent = message;
        this.status.hidden = hide;
    };

    /** Mengikat seluruh tombol [data-cv-open] di halaman. */
    function initOpeners() {
        document.querySelectorAll('[data-cv-open]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var src = button.getAttribute('data-cv-src');

                if (!src) {
                    return;
                }

                var flipbook = new Flipbook(src, button.getAttribute('data-cv-title'));
                flipbook.open();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOpeners);
    } else {
        initOpeners();
    }
})();
