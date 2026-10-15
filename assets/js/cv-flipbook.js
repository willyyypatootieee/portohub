/**
 * Flipbook CV berbasis PDF.js.
 *
 * Setiap halaman PDF dirender ke <canvas>, lalu dibalik dengan animasi 3D CSS
 * (teknik flip book: https://github.com/fchavonet/creative_coding-flip_book).
 * Dipakai di halaman Profil & CV creator dan di portofolio publik, sehingga
 * pengunjung membaca CV seperti membuka buku, bukan menonton tab PDF.
 *
 * PDF.js dimuat dari CDN saat pertama kali dibutuhkan (lazy), jadi halaman
 * yang tidak membuka CV tidak membayar biaya unduhannya.
 */
(function () {
    'use strict';

    var PDFJS_VERSION = '4.6.82';
    var PDFJS_BASE = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VERSION + '/build/';
    var pdfjsPromise = null;

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
                    '<button class="cv-flipbook-close" type="button" data-cv-close aria-label="Tutup">✕</button>' +
                '</div>' +
                '<p class="cv-flipbook-status" data-cv-status hidden></p>' +
                '<div class="cv-stage">' +
                    '<button type="button" class="button button-outline" data-cv-prev aria-label="Halaman sebelumnya">‹</button>' +
                    '<div class="cv-book" data-cv-book></div>' +
                    '<button type="button" class="button button-outline" data-cv-next aria-label="Halaman berikutnya">›</button>' +
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
        this.current = 0;
        this.scale = 1.4;
        this.modal = createModal();
        this.book = this.modal.querySelector('[data-cv-book]');
        this.status = this.modal.querySelector('[data-cv-status]');
        this.count = this.modal.querySelector('[data-cv-count]');
        this.prevButton = this.modal.querySelector('[data-cv-prev]');
        this.nextButton = this.modal.querySelector('[data-cv-next]');
        this.modal.querySelector('[data-cv-modal-title]').textContent = title || 'CV';

        this.bindEvents();
    }

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

        loadPdfJs()
            .then(function (pdfjs) {
                return pdfjs.getDocument(self.src).promise;
            })
            .then(function (pdf) {
                self.pdf = pdf;
                self.pageCount = pdf.numPages;
                self.setStatus('', true);
                return renderPage(pdf, 1, self.scale);
            })
            .then(function (canvas) {
                self.book.appendChild(self.wrapLeaf(canvas, null));
                self.showSpread();
            })
            .catch(function (error) {
                self.setStatus('CV tidak dapat dimuat: ' + error.message, false);
            });
    };

    /** Membungkus canvas jadi "lembar" yang bisa dibalik. */
    Flipbook.prototype.wrapLeaf = function (frontCanvas, backCanvas) {
        var leaf = document.createElement('div');
        leaf.className = 'cv-leaf';

        var front = document.createElement('div');
        front.className = 'cv-leaf-face is-front';
        front.appendChild(frontCanvas);
        leaf.appendChild(front);

        if (backCanvas !== null) {
            var back = document.createElement('div');
            back.className = 'cv-leaf-face is-back';
            back.appendChild(backCanvas);
            leaf.appendChild(back);
        }

        return leaf;
    };

    /**
     * Menampilkan pasangan halaman pada posisi `current`.
     *
     * Cara sederhana dan kokoh: satu lembar per halaman, dibalik dengan
     * rotasi 3D. Halaman berikutnya disiapkan di belakang lembar aktif
     * supaya animasi terlihat seperti membuka buku.
     */
    Flipbook.prototype.showSpread = function () {
        var self = this;

        this.book.innerHTML = '';
        this.count.textContent = 'Halaman ' + (this.current + 1) + ' / ' + this.pageCount;
        this.prevButton.disabled = this.current === 0;
        this.nextButton.disabled = this.current >= this.pageCount - 1;

        var layers = [];

        // Lembar-lembar di belakang (yang belum dibuka), dari terluar ke dalam.
        for (var i = this.pageCount - 1; i > this.current; i -= 1) {
            layers.push(i);
        }

        // Halaman aktif dirender paling atas.
        layers.push(this.current);

        layers.forEach(function (pageIndex) {
            renderPage(self.pdf, pageIndex + 1, self.scale).then(function (canvas) {
                var leaf = self.wrapLeaf(canvas, null);
                leaf.style.zIndex = String(self.pageCount - pageIndex);

                if (pageIndex !== self.current) {
                    leaf.style.position = 'absolute';
                }

                self.book.appendChild(leaf);
            });
        });
    };

    Flipbook.prototype.next = function () {
        if (this.current >= this.pageCount - 1) {
            return;
        }

        var leaf = this.book.lastElementChild;

        if (leaf) {
            leaf.classList.add('is-flipped');
        }

        var self = this;

        window.setTimeout(function () {
            self.current += 1;
            self.showSpread();
        }, 300);
    };

    Flipbook.prototype.previous = function () {
        if (this.current === 0) {
            return;
        }

        this.current -= 1;
        this.showSpread();
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
