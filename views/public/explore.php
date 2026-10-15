<?php
/**
 * Konten halaman eksplorasi karya. Dibungkus oleh views/layout/header.php dan
 * footer.php sehingga tidak memuat <html>/<body> sendiri.
 *
 * @var array<int, string> $categories
 * @var array<int, array<string, mixed>> $projects
 * @var array<int, array<string, mixed>> $creators
 * @var string $search
 * @var string $category
 * @var string $tab
 * @var int $projectTotal
 */

$exploreBase = appUrl('/explore/');

// Bangun URL eksplorasi dengan mempertahankan filter aktif, lalu menimpanya
// sesuai kebutuhan tiap tautan (tab, kategori, kata kunci).
$exploreLink = static function (array $overrides) use ($exploreBase, $tab, $search, $category): string {
    $params = array_merge(['tab' => $tab, 'q' => $search, 'category' => $category], $overrides);
    $params = array_filter($params, static fn ($value): bool => $value !== '' && $value !== null);

    return $params === [] ? $exploreBase : $exploreBase . '?' . http_build_query($params);
};

// Ambil satu sampai dua huruf awal dari sebuah nama untuk avatar teks.
$initials = static function (string $name): string {
    $result = '';

    foreach (preg_split('/\s+/', trim($name)) ?: [] as $word) {
        if ($word === '') {
            continue;
        }

        $result .= mb_strtoupper(mb_substr($word, 0, 1));

        if (mb_strlen($result) >= 2) {
            break;
        }
    }

    return $result === '' ? '✦' : $result;
};

$isCreators = $tab === 'creators';
$hasFilters = $search !== '' || $category !== '';
?>
<section class="explore-hero" id="top">
    <div class="container">
        <p class="eyebrow">Explore</p>
        <h1>Find your next<br>favourite <em>creator.</em></h1>
        <p class="explore-hero-intro">
            Browse published work from independent creatives. Filter by category,
            or search across projects and people.
        </p>
    </div>
</section>

<section class="explore section">
    <div class="container">
        <form class="explore-toolbar" method="get" action="<?= escape($exploreBase) ?>" role="search">
            <input type="hidden" name="tab" value="<?= escape($tab) ?>">
            <?php if (!$isCreators && $category !== ''): ?>
                <input type="hidden" name="category" value="<?= escape($category) ?>">
            <?php endif; ?>

            <label class="search-box explore-search">
                <span class="sr-only">Search projects and creators</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6"/>
                    <path d="m16 16 4 4"/>
                </svg>
                <input id="explore-search" type="search" name="q" value="<?= escape($search) ?>" placeholder="Search projects or creators">
                <kbd>⌘ K</kbd>
            </label>

            <button class="button button-small explore-submit" type="submit">Search</button>

            <nav class="explore-tabs" aria-label="Result type">
                <a
                    class="explore-tab<?= $isCreators ? '' : ' is-active' ?>"
                    <?= $isCreators ? '' : 'aria-current="page"' ?>
                    href="<?= escape($exploreLink(['tab' => 'projects'])) ?>"
                >Projects</a>
                <a
                    class="explore-tab<?= $isCreators ? ' is-active' : '' ?>"
                    <?= $isCreators ? 'aria-current="page"' : '' ?>
                    href="<?= escape($exploreLink(['tab' => 'creators', 'category' => ''])) ?>"
                >Creators</a>
            </nav>
        </form>

        <?php if (!$isCreators && $categories !== []): ?>
            <div class="explore-filters" aria-label="Filter by category">
                <a class="chip<?= $category === '' ? ' is-active' : '' ?>" href="<?= escape($exploreLink(['category' => ''])) ?>">All work</a>
                <?php foreach ($categories as $item): ?>
                    <a class="chip<?= $category === $item ? ' is-active' : '' ?>" href="<?= escape($exploreLink(['category' => $item])) ?>"><?= escape($item) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="explore-meta">
            <p>
                <?php if ($isCreators): ?>
                    <?= count($creators) ?> creator<?= count($creators) === 1 ? '' : 's' ?>
                <?php else: ?>
                    <?= (int) $projectTotal ?> project<?= (int) $projectTotal === 1 ? '' : 's' ?>
                <?php endif; ?>
                <?php if ($search !== ''): ?>
                    for <strong>“<?= escape($search) ?>”</strong>
                <?php endif; ?>
                <?php if (!$isCreators && $category !== ''): ?>
                    in <strong><?= escape($category) ?></strong>
                <?php endif; ?>
            </p>
            <?php if ($hasFilters): ?>
                <a class="explore-reset" href="<?= escape($exploreLink(['q' => '', 'category' => ''])) ?>">Reset filters</a>
            <?php endif; ?>
        </div>

        <?php if ($isCreators): ?>
            <?php if ($creators === []): ?>
                <div class="explore-empty">
                    <strong>No creators found</strong>
                    <p>Try a different keyword, or reset the search to see everyone with published work.</p>
                    <a class="button button-secondary" href="<?= escape($exploreLink(['q' => '', 'category' => ''])) ?>">Reset search</a>
                </div>
            <?php else: ?>
                <div class="creator-grid">
                    <?php foreach ($creators as $creator): ?>
                        <?php
                        $creatorLink = appUrl('/portfolio/' . rawurlencode((string) $creator['username']) . '/');
                        $creatorCover = $creator['cover'] ?? null;
                        $creatorCoverUrl = $creatorCover !== null
                            ? appUrl('/assets/uploads/' . rawurlencode((string) $creatorCover['stored_name']))
                            : null;
                        ?>
                        <article class="creator-card">
                            <a class="creator-card-cover" href="<?= escape($creatorLink) ?>" tabindex="-1" aria-hidden="true">
                                <?php if ($creatorCoverUrl !== null): ?>
                                    <img src="<?= escape($creatorCoverUrl) ?>" alt="" loading="lazy">
                                <?php else: ?>
                                    <span class="creator-card-initials"><?= escape($initials((string) $creator['display_name'])) ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="creator-card-body">
                                <span class="creator-badge" aria-hidden="true"><?= escape($initials((string) $creator['display_name'])) ?></span>
                                <div>
                                    <h3><a href="<?= escape($creatorLink) ?>"><?= escape($creator['display_name']) ?></a></h3>
                                    <p class="creator-card-headline"><?= escape($creator['headline']) ?></p>
                                </div>
                                <p class="creator-card-stats">
                                    <?= (int) $creator['project_count'] ?> project<?= (int) $creator['project_count'] === 1 ? '' : 's' ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($projects === []): ?>
                <div class="explore-empty">
                    <strong>No work found</strong>
                    <p>Nothing matched your search. Try another keyword or pick a different category.</p>
                    <a class="button button-secondary" href="<?= escape($exploreLink(['q' => '', 'category' => ''])) ?>">Clear filters</a>
                </div>
            <?php else: ?>
                <div class="portfolio-grid" id="portfolio-grid">
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $cover = $project['cover'] ?? null;
                        $coverUrl = $cover !== null
                            ? appUrl('/assets/uploads/' . rawurlencode((string) $cover['stored_name']))
                            : null;
                        $projectLink = appUrl('/project/' . (int) $project['id']);
                        $creatorLink = appUrl('/portfolio/' . rawurlencode((string) $project['username']) . '/');
                        ?>
                        <article class="work-card">
                            <div class="work-image">
                                <a class="work-cover-link" href="<?= escape($projectLink) ?>" aria-label="View <?= escape($project['title']) ?>">
                                    <?php if ($coverUrl !== null): ?>
                                        <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?> by <?= escape($project['display_name']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <span class="work-placeholder"><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                </a>
                                <span class="work-category"><?= escape($project['category']) ?></span>
                                <span class="work-view-hint" aria-hidden="true">View project <span>→</span></span>
                            </div>

                            <div class="work-details">
                                <div>
                                    <h3><a href="<?= escape($projectLink) ?>"><?= escape($project['title']) ?></a></h3>
                                    <p><a href="<?= escape($creatorLink) ?>"><?= escape($project['display_name']) ?></a></p>
                                </div>
                                <p class="work-stats">
                                    <?= (int) $project['media_count'] ?> file<?= (int) $project['media_count'] === 1 ? '' : 's' ?>
                                </p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
