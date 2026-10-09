
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

    document.querySelectorAll('.save-button').forEach(function (button) {
        button.addEventListener('click', function () {
            const isSaved = button.classList.toggle('is-saved');
            button.textContent = isSaved ? '♥' : '♡';
            button.setAttribute('aria-pressed', String(isSaved));
        });
    });
}());
