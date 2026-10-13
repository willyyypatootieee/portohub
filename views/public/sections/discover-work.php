<section class="discover section" id="discover">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Curated daily</p>
                <h2>Discover remarkable work</h2>
                <p class="section-intro">A daily selection of standout projects from independent creatives.</p>
            </div>
            <a class="button button-secondary" href="#portfolio-grid">View all work <span aria-hidden="true">→</span></a>
        </div>

        <div class="work-controls">
            <div class="filter-list" role="tablist" aria-label="Filter work by category">
                <button class="filter-button is-active" type="button" data-filter="all" role="tab" aria-selected="true">
                    All work
                </button>
                <?php foreach ($portfolioCategories as $category): ?>
                    <button class="filter-button" type="button" data-filter="<?= escape($category) ?>" role="tab" aria-selected="false">
                        <?= escape($category) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <label class="search-box">
                <span class="sr-only">Search work</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6"/>
                    <path d="m16 16 4 4"/>
                </svg>
                <input id="work-search" type="search" placeholder="Search work">
                <kbd>⌘ K</kbd>
            </label>
        </div>

        <div class="portfolio-grid" id="portfolio-grid">
            <?php foreach ($portfolioItems as $item): ?>
                <article
                    class="work-card <?= (int) $item['is_featured'] === 1 ? 'work-card-featured' : '' ?>"
                    data-category="<?= escape($item['category']) ?>"
                    data-search="<?= escape(strtolower($item['title'] . ' ' . $item['creator_name'] . ' ' . $item['category'])) ?>"
                >
                    <div class="work-image">
                        <img src="assets/images/<?= escape($item['image_filename']) ?>" alt="<?= escape($item['title']) ?> by <?= escape($item['creator_name']) ?>">
                        <span class="work-category"><?= escape($item['category']) ?></span>
                        <button class="save-button" type="button" aria-label="Save <?= escape($item['title']) ?>" title="Save this work">
                            ♡
                        </button>
                    </div>
                    <div class="work-details">
                        <div>
                            <h3><?= escape($item['title']) ?></h3>
                            <p><?= escape($item['creator_name']) ?></p>
                        </div>
                        <p class="work-stats">
                            ♡ <?= shortNumber((int) $item['likes_count']) ?>
                            <span>◉ <?= shortNumber((int) $item['views_count']) ?></span>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="empty-results" hidden>No work matches your search. Try another term or category.</p>
    </div>
</section>
