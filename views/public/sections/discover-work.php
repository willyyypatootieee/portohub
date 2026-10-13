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
                <?php foreach ($showcaseCategories as $category): ?>
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
            <?php if ($showcaseProjects === []): ?>
                <article class="work-card work-card-empty">
                    <div class="work-image">
                        <span class="empty-work-mark">✦</span>
                    </div>
                    <div class="work-details">
                        <div>
                            <h3>Become The One</h3>
                            <p>Belum ada karya yang dipublikasikan. Jadi yang pertama dan tunjukkan karyamu di sini.</p>
                        </div>
                        <a class="button" href="<?= escape(appUrl('/login/register/')) ?>">Mulai berkarya</a>
                    </div>
                </article>
            <?php endif; ?>

            <?php foreach ($showcaseProjects as $project): ?>
                <?php
                $cover = $project['cover'] ?? null;
                $coverUrl = $cover !== null
                    ? appUrl('/assets/uploads/' . rawurlencode((string) $cover['stored_name']))
                    : null;
                $creatorLink = appUrl('/portfolio/' . rawurlencode((string) $project['username']));
                ?>
                <article
                    class="work-card"
                    data-category="<?= escape($project['category']) ?>"
                    data-search="<?= escape(strtolower($project['title'] . ' ' . $project['display_name'] . ' ' . $project['category'])) ?>"
                >
                    <div class="work-image">
                        <?php if ($coverUrl !== null): ?>
                            <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?> by <?= escape($project['display_name']) ?>">
                        <?php else: ?>
                            <span class="work-placeholder"><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                        <?php endif; ?>

                        <span class="work-category"><?= escape($project['category']) ?></span>
                        <button class="save-button" type="button" aria-label="Save <?= escape($project['title']) ?>" title="Save this work">
                            ♡
                        </button>
                    </div>

                    <div class="work-details">
                        <div>
                            <h3>
                                <a href="<?= escape($creatorLink) ?>"><?= escape($project['title']) ?></a>
                            </h3>
                            <p>
                                <a href="<?= escape($creatorLink) ?>"><?= escape($project['display_name']) ?></a>
                            </p>
                        </div>
                        <p class="work-stats">
                            <?= (int) $project['media_count'] ?> file<?= (int) $project['media_count'] === 1 ? '' : 's' ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="empty-results" hidden>Become The One — belum ada karya yang cocok dengan filter ini. Coba kata kunci atau kategori lain.</p>
    </div>
</section>
