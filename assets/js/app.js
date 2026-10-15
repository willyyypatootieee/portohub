
(function () {
    'use strict';

    const menuButton = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('.primary-navigation');
    const themeButton = document.querySelector('.theme-toggle');
    const filterButtons = document.querySelectorAll('.filter-button');
    const workCards = document.querySelectorAll('.work-card');
    const searchInput = document.querySelector('#work-search');
    const emptyMessage = document.querySelector('.empty-results');

    // Mobile menu
    if (menuButton && navigation) {
        menuButton.addEventListener('click', function () {
            const isOpen = navigation.classList.toggle('is-open');
            menuButton.setAttribute('aria-expanded', String(isOpen));
            menuButton.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
        });

        navigation.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navigation.classList.remove('is-open');
                menuButton.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Menu akun (nama creator yang sedang login)
    const accountMenu = document.querySelector('[data-account-menu]');

    if (accountMenu) {
        const accountTrigger = accountMenu.querySelector('[data-account-trigger]');
        const accountDropdown = accountMenu.querySelector('[data-account-dropdown]');

        if (accountTrigger && accountDropdown) {
            function closeAccountMenu() {
                accountDropdown.hidden = true;
                accountTrigger.setAttribute('aria-expanded', 'false');
            }

            accountTrigger.addEventListener('click', function (event) {
                event.stopPropagation();
                const willOpen = accountDropdown.hidden;
                accountDropdown.hidden = !willOpen;
                accountTrigger.setAttribute('aria-expanded', String(willOpen));
            });

            document.addEventListener('click', function (event) {
                if (!accountMenu.contains(event.target)) {
                    closeAccountMenu();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAccountMenu();
                    accountTrigger.focus();
                }
            });
        }
    }

    let activeCategory = 'all';
    function updateVisibleCards() {
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;
        workCards.forEach(function (card) {
            const isCorrectCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            const matchesSearch = !searchTerm || card.dataset.search.includes(searchTerm);
            const shouldShow = isCorrectCategory && matchesSearch;
            card.classList.toggle('is-hidden', !shouldShow);
            if (shouldShow) visibleCount += 1;
        });
        if (emptyMessage) emptyMessage.hidden = visibleCount !== 0;
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activeCategory = button.dataset.filter;
            filterButtons.forEach(function (item) {
                const isSelected = item === button;
                item.classList.toggle('is-active', isSelected);
                item.setAttribute('aria-selected', String(isSelected));
            });
            updateVisibleCards();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', updateVisibleCards);
        document.addEventListener('keydown', function (event) {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                searchInput.focus();
            }
        });
    }

    // Halaman eksplorasi memakai pencarian di sisi server (GET form), jadi
    // hanya pintasan papan tik yang ditambahkan di sini.
    const exploreSearch = document.querySelector('#explore-search');

    if (exploreSearch) {
        document.addEventListener('keydown', function (event) {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                exploreSearch.focus();
            }
        });
    }

    // Tombol simpan hanya mengganti status; ikon hati memakai <svg> di markup
    // dan terisi otomatis lewat CSS .is-saved, jadi tidak perlu ganti teks.
    document.querySelectorAll('.save-button').forEach(function (button) {
        button.addEventListener('click', function () {
            const isSaved = button.classList.toggle('is-saved');
            button.setAttribute('aria-pressed', String(isSaved));
        });
    });

    // Modal login/register di landing page.
    // Tanpa JavaScript, tautan pemicu tetap membuka halaman /login/.
    const authModal = document.querySelector('[data-auth-modal]');

    if (authModal) {
        const authOpeners = document.querySelectorAll('[data-auth-open]');
        const authClosers = authModal.querySelectorAll('[data-auth-close]');
        const authTabs = authModal.querySelectorAll('[data-auth-tab]');
        const authPanels = authModal.querySelectorAll('[data-auth-panel]');
        let lastFocusedElement = null;

        function showAuthTab(name) {
            authTabs.forEach(function (tab) {
                const isActive = tab.getAttribute('data-auth-tab') === name;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', String(isActive));
            });
            authPanels.forEach(function (panel) {
                panel.hidden = panel.getAttribute('data-auth-panel') !== name;
            });

            const panel = authModal.querySelector('[data-auth-panel="' + name + '"]');
            const firstField = panel ? panel.querySelector('input:not([type="hidden"])') : null;
            if (firstField) firstField.focus();
        }

        function openAuthModal(name) {
            lastFocusedElement = document.activeElement;
            authModal.hidden = false;
            document.documentElement.classList.add('has-modal');
            showAuthTab(name || 'login');
        }

        function closeAuthModal() {
            authModal.hidden = true;
            document.documentElement.classList.remove('has-modal');
            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }

        authOpeners.forEach(function (opener) {
            opener.addEventListener('click', function (event) {
                event.preventDefault();
                openAuthModal(opener.getAttribute('data-auth-open'));
            });
        });

        authClosers.forEach(function (closer) {
            closer.addEventListener('click', closeAuthModal);
        });

        authTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                showAuthTab(tab.getAttribute('data-auth-tab'));
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !authModal.hidden) {
                closeAuthModal();
            }
        });

        // Tahan fokus (focus trap) selama modal terbuka.
        authModal.addEventListener('keydown', function (event) {
            if (event.key !== 'Tab' || authModal.hidden) return;

            const focusable = authModal.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), [tabindex]:not([tabindex="-1"])'
            );
            const visible = [];
            focusable.forEach(function (element) {
                if (element.offsetParent !== null) visible.push(element);
            });
            if (visible.length === 0) return;

            const first = visible[0];
            const last = visible[visible.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        function showAuthErrors(form, errors, alertBox) {
            const general = errors.login || errors.general || '';
            if (general && alertBox) {
                alertBox.textContent = general;
                alertBox.hidden = false;
            }

            Object.keys(errors).forEach(function (field) {
                if (field === 'login' || field === 'general') return;
                const input = form.querySelector('[name="' + field + '"]');
                if (!input) return;
                const message = document.createElement('small');
                message.className = 'auth-error';
                message.setAttribute('data-auth-generated', '');
                message.textContent = errors[field];
                input.insertAdjacentElement('afterend', message);
                input.setAttribute('aria-invalid', 'true');
            });
        }

        // Kirim form lewat fetch supaya error tampil di dalam modal.
        authModal.querySelectorAll('[data-auth-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (typeof window.fetch !== 'function') return;

                event.preventDefault();

                form.querySelectorAll('.auth-error[data-auth-generated]').forEach(function (node) {
                    node.remove();
                });
                form.querySelectorAll('[aria-invalid="true"]').forEach(function (input) {
                    input.removeAttribute('aria-invalid');
                });

                const alertBox = form.querySelector('[data-auth-alert]');
                if (alertBox) {
                    alertBox.hidden = true;
                    alertBox.textContent = '';
                }

                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) submitButton.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        return response.json().catch(function () { return null; });
                    })
                    .then(function (payload) {
                        // Server membalas HTML (bukan JSON): jalankan submit biasa.
                        if (!payload) {
                            form.submit();
                            return;
                        }
                        if (payload.ok && payload.redirect) {
                            window.location.assign(payload.redirect);
                            return;
                        }
                        if (submitButton) submitButton.disabled = false;
                        showAuthErrors(form, payload.errors || {}, alertBox);
                    })
                    .catch(function () {
                        if (submitButton) submitButton.disabled = false;
                        form.submit();
                    });
            });
        });
    }
}());
