<section class="discover section" id="discover">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Curated daily</p>
                <h2>Discover remarkable work</h2>
                <p class="section-intro">A daily selection of standout projects from independent creatives.</p>
            </div>
            <a class="button button-secondary" href="<?= escape(appUrl('/explore/')) ?>">
                View all work
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14"/>
                    <path d="m13 6 6 6-6 6"/>
                </svg>
            </a>
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
                        <span class="empty-work-mark">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="4.5" width="18" height="15" rx="2"/>
                                <path d="M3 9h18"/>
                                <circle cx="6.2" cy="6.7" r=".6"/>
                                <circle cx="8.4" cy="6.7" r=".6"/>
                                <path d="m8 16 3-3 2.4 2.4L17 12l2 2.2"/>
                            </svg>
                        </span>
                    </div>
                    <div class="work-details">
                        <div>
                            <h3>Become The One</h3>
                            <p>Belum ada karya yang dipublikasikan. Jadi yang pertama dan tunjukkan karyamu di sini.</p>
                        </div>
                        <a class="button" href="<?= escape(appUrl('/login/register/')) ?>" data-auth-open="register">Mulai berkarya</a>
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
                    data-search="<?= escape(strtolower($project['title'] . ' ' . $project['display_name'] . ' ' . $project['category'] . ' ' . (string) ($project['technologies'] ?? ''))) ?>"
                >
                    <div class="work-image">
                        <?php if ($coverUrl !== null): ?>
                            <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?> by <?= escape($project['display_name']) ?>">
                        <?php else: ?>
                            <span class="work-placeholder"><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                        <?php endif; ?>

                        <span class="work-category"><?= escape($project['category']) ?></span>
                        <button class="save-button" type="button" aria-label="Save <?= escape($project['title']) ?>" title="Save this work">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 20s-7-4.4-7-9.4A4.1 4.1 0 0 1 12 7.7a4.1 4.1 0 0 1 7 2.9c0 5-7 9.4-7 9.4Z"/>
                            </svg>
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

                    <?php
                    $cardTechnologies = array_values(array_filter(array_map(
                        static fn (string $item): string => trim($item),
                        explode(',', (string) ($project['technologies'] ?? ''))
                    ), static fn (string $item): bool => $item !== ''));
                    ?>

                    <?php if ($cardTechnologies !== []): ?>
                        <ul class="work-tech" aria-label="Teknologi yang digunakan">
                            <?php foreach (array_slice($cardTechnologies, 0, 4) as $technology): ?>
                                <li><?= escape($technology) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="empty-results" hidden>Become The One — belum ada karya yang cocok dengan filter ini. Coba kata kunci atau kategori lain.</p>
    </div>
</section>
