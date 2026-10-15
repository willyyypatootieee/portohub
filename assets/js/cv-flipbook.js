/**
 * Flipbook CV berbasis PDF.js.
 *
 * PDF dirender per halaman ke <canvas>, lalu dibalik dengan animasi 3D CSS
 * (teknik flip book: https://github.com/fchavonet/creative_coding-flip_book).
 * Mode buku: dua halaman tampil berdampingan seperti buku terbuka; halaman
 * pertama (dan terakhir ganjil) sendirian di kanan, seperti buku sungguhan.
 *
 * Modal punya tombol layar penuh (Fullscreen API) dan tombol "buka di tab
 * baru" yang mengarah ke sumber PDF langsung.
 *
 * PDF.js dimuat dari CDN saat pertama kali dibutuhkan (lazy), jadi halaman
 * yang tidak membuka CV tidak membayar biaya unduhannya.
 */
(function () {
    'use strict';

    var PDFJS_VERSION = '4.6.82';
    var PDFJS_BASE = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@' + PDFJS_VERSION + '/build/';
    var pdfjsPromise = null;
    var FLIP_MS = 380;

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
                    '<div class="cv-flipbook-tools">' +
                        '<button class="cv-tool" type="button" data-cv-fullscreen aria-label="Layar penuh" title="Layar penuh">' +
                            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3H3v5"/><path d="M16 3h5v5"/><path d="M8 21H3v-5"/><path d="M16 21h5v-5"/></svg>' +
                        '</button>' +
                        '<a class="cv-tool" data-cv-download href="#" aria-label="Unduh PDF" title="Unduh PDF" download>' +
                            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v11"/><path d="m7 11 5 5 5-5"/><path d="M5 20h14"/></svg>' +
                        '</a>' +
                        '<a class="cv-tool" data-cv-newtab href="#" target="_blank" rel="noopener" aria-label="Buka di tab baru" title="Buka di tab baru">' +
                            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7"/><path d="M9 7h8v8"/></svg>' +
                        '</a>' +
                        '<button class="cv-tool" type="button" data-cv-close aria-label="Tutup" title="Tutup">' +
                            '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12"/><path d="m18 6-12 12"/></svg>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<p class="cv-flipbook-status" data-cv-status hidden></p>' +
                '<div class="cv-stage">' +
                    '<button type="button" class="cv-nav cv-nav-prev" data-cv-prev aria-label="Halaman sebelumnya">' +
                        '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg>' +
                    '</button>' +
                    '<div class="cv-book" data-cv-book></div>' +
                    '<button type="button" class="cv-nav cv-nav-next" data-cv-next aria-label="Halaman berikutnya">' +
                        '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m10 6 6 6-6 6"/></svg>' +
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
        /** Nomor halaman kiri spread (1-based); 0 berarti belum dibuka. */
        this.leftPage = 0;
        this.scale = 1.4;
        this.modal = createModal();
        this.book = this.modal.querySelector('[data-cv-book]');
        this.status = this.modal.querySelector('[data-cv-status]');
        this.count = this.modal.querySelector('[data-cv-count]');
        this.prevButton = this.modal.querySelector('[data-cv-prev]');
        this.nextButton = this.modal.querySelector('[data-cv-next]');
        this.newTab = this.modal.querySelector('[data-cv-newtab]');
        this.downloadLink = this.modal.querySelector('[data-cv-download]');
        this.fullscreenButton = this.modal.querySelector('[data-cv-fullscreen]');
        this.dialog = this.modal.querySelector('.cv-flipbook-dialog');
        this.modal.querySelector('[data-cv-modal-title]').textContent = title || 'CV';
        this.newTab.href = src;
        this.downloadLink.href = src + (src.indexOf('?') === -1 ? '?' : '&') + 'download=1';

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

        this.fullscreenButton.addEventListener('click', function () {
            self.toggleFullscreen();
        });

        document.addEventListener('fullscreenchange', function () {
            self.modal.classList.toggle('is-fullscreen', document.fullscreenElement === self.modal);
        });

        document.addEventListener('keydown', function (event) {
            if (self.modal.hidden) {
                return;
            }

            if (event.key === 'Escape' && document.fullscreenElement !== self.modal) {
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

    Flipbook.prototype.toggleFullscreen = function () {
        if (document.fullscreenElement === this.modal) {
            document.exitFullscreen();
        } else {
            this.modal.requestFullscreen().catch(function () {
                /* Browser menolak (mis. tanpa izin pengguna) — abaikan. */
            });
        }
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
                self.showSpread();
            })
            .catch(function (error) {
                self.setStatus('CV tidak dapat dimuat: ' + error.message, false);
            });
    };

    /** Membungkus satu-dua canvas jadi "lembar" yang bisa dibalik. */
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

    /** Wadah satu halaman dalam spread dua halaman. */
    Flipbook.prototype.wrapPage = function (canvas, position) {
        var slot = document.createElement('div');
        slot.className = 'cv-page cv-page-' + position;
        slot.appendChild(canvas);
        return slot;
    };

    /**
     * Menggambar spread dua halaman.
     *
     * `this.leftPage` adalah nomor halaman kiri (1-based). Nilai 0 berarti
     * belum dibuka: hanya halaman 1 yang tampil di kanan (seperti sampul).
     * Setelah itu halaman berpasangan: (2,3), (4,5), dst.
     */
    Flipbook.prototype.showSpread = function () {
        var self = this;

        this.book.innerHTML = '';
        this.newTab.href = this.src;
        this.downloadLink.href = this.src + (this.src.indexOf('?') === -1 ? '?' : '&') + 'download=1';

        var left = this.leftPage;                 // 0 = cover
        var right = left === 0 ? 1 : left + 1;    // halaman kanan

        // Tandai apakah spread menampilkan dua halaman sekaligus.
        this.book.classList.toggle('is-single', left === 0);

        if (left === 0) {
            this.count.textContent = 'Halaman 1 / ' + this.pageCount;
        } else if (right <= this.pageCount) {
            this.count.textContent = 'Halaman ' + left + '–' + right + ' / ' + this.pageCount;
        } else {
            this.count.textContent = 'Halaman ' + left + ' / ' + this.pageCount;
        }

        this.prevButton.disabled = left === 0;
        this.nextButton.disabled = right >= this.pageCount;

        // Halaman kiri statis (sudah terbuka).
        if (left >= 1) {
            renderPage(this.pdf, left, this.scale).then(function (canvas) {
                self.book.appendChild(self.wrapPage(canvas, 'left'));
            });
        }

        // Lembar kanan: depan = halaman kanan sekarang, belakang = halaman
        // kiri spread berikutnya, supaya animasi balik terasa nyata.
        var backPage = right + 1 <= this.pageCount ? right + 1 : null;
        var frontPromise = renderPage(this.pdf, right, this.scale);
        var backPromise = backPage !== null
            ? renderPage(this.pdf, backPage, this.scale)
            : Promise.resolve(null);

        Promise.all([frontPromise, backPromise]).then(function (result) {
            var leaf = self.wrapLeaf(result[0], result[1]);
            leaf.classList.add('is-spread-right');
            self.book.appendChild(leaf);
        });
    };

    /** Halaman kiri berikutnya (dipakai untuk maju). */
    Flipbook.prototype.nextLeftPage = function () {
        return this.leftPage === 0 ? 2 : this.leftPage + 2;
    };

    Flipbook.prototype.next = function () {
        if (this.nextButton.disabled) {
            return;
        }

        var leaf = this.book.querySelector('.cv-leaf.is-spread-right');
        var self = this;
        var target = this.nextLeftPage();

        if (leaf) {
            leaf.classList.add('is-flipped');
            window.setTimeout(function () {
                self.leftPage = target;
                self.showSpread();
            }, FLIP_MS);
        } else {
            this.leftPage = target;
            this.showSpread();
        }
    };

    Flipbook.prototype.previous = function () {
        if (this.leftPage === 0) {
            return;
        }

        var target = this.leftPage === 2 ? 0 : this.leftPage - 2;
        this.leftPage = Math.max(0, target);
        this.showSpread();
    };

    Flipbook.prototype.close = function () {
        if (document.fullscreenElement === this.modal) {
            document.exitFullscreen();
        }

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
