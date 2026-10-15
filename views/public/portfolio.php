<?php
/**
 * Konten halaman portofolio publik satu creator. Dibungkus oleh layout
 * header/footer sehingga tidak memuat <html>/<body> sendiri.
 *
 * @var array<string, mixed> $creator
 * @var array<int, array<string, mixed>> $projects
 */

$creatorInitial = mb_strtoupper(mb_substr((string) $creator['display_name'], 0, 1));

$totalMedia = 0;
foreach ($projects as $project) {
    $totalMedia += count($project['media'] ?? []);
}

$memberSince = (string) ($creator['created_at'] ?? '');
$memberSinceYear = $memberSince !== '' ? date('Y', strtotime($memberSince)) : SITE_YEAR;
?>
<section class="portfolio-hero" id="top">
    <div class="container">
        <div class="portfolio-hero-top">
            <span class="portfolio-avatar" aria-hidden="true"><?= escape($creatorInitial) ?></span>
            <div class="portfolio-hero-copy">
                <p class="eyebrow">Independent creative</p>
                <h1><?= escape($creator['display_name']) ?></h1>
                <p class="portfolio-headline"><?= escape($creator['headline']) ?></p>
            </div>
        </div>

        <p class="portfolio-bio"><?= nl2br(escape($creator['bio'])) ?></p>

        <dl class="portfolio-stats">
            <div>
                <dt>Published works</dt>
                <dd><?= count($projects) ?></dd>
            </div>
            <div>
                <dt>Media files</dt>
                <dd><?= $totalMedia ?></dd>
            </div>
            <div>
                <dt>Member since</dt>
                <dd><?= escape($memberSinceYear) ?></dd>
            </div>
        </dl>
    </div>
</section>

<section class="portfolio-work section">
    <div class="container">
        <div class="section-heading">
            <h2>Selected work</h2>
            <span>
                <?= count($projects) ?> published project<?= count($projects) === 1 ? '' : 's' ?>
            </span>
        </div>

        <?php if ($projects === []): ?>
            <div class="empty-state">
                <strong>Become The One</strong>
                <p>Creator ini sedang menyiapkan karya berikutnya.</p>
            </div>
        <?php else: ?>
            <div class="public-work-grid">
                <?php foreach ($projects as $project): ?>
                    <?php
                    $cover = $project['cover'] ?? null;
                    $coverUrl = $cover !== null
                        ? appUrl('/assets/uploads/' . rawurlencode((string) $cover['stored_name']))
                        : null;
                    $media = $project['media'] ?? [];
                    $coverStored = (string) ($cover['stored_name'] ?? '');
                    $gallery = array_values(array_filter(
                        $media,
                        static fn (array $item): bool => (string) ($item['stored_name'] ?? '') !== $coverStored
                    ));
                    $projectLink = appUrl('/project/' . (int) $project['id']);

                    $technologies = array_values(array_filter(array_map(
                        static fn (string $item): string => trim($item),
                        explode(',', (string) ($project['technologies'] ?? ''))
                    ), static fn (string $item): bool => $item !== ''));
                    ?>
                    <article class="public-work-card">
                        <a class="public-thumb" href="<?= escape($projectLink) ?>">
                            <?php if ($coverUrl !== null): ?>
                                <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <span><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                            <?php endif; ?>
                            <span class="public-thumb-hint">View project <span aria-hidden="true">→</span></span>
                        </a>

                        <div class="public-work-meta">
                            <p class="public-work-category"><?= escape($project['category']) ?></p>
                            <h3><a href="<?= escape($projectLink) ?>"><?= escape($project['title']) ?></a></h3>
                            <span class="public-work-description"><?= nl2br(escape($project['description'])) ?></span>
                        </div>

                        <?php if ($technologies !== []): ?>
                            <ul class="public-tech-chips" aria-label="Teknologi yang digunakan">
                                <?php foreach ($technologies as $technology): ?>
                                    <li><?= escape($technology) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($project['demo_url']) || !empty($project['repo_url'])): ?>
                            <div class="public-project-links">
                                <?php if (!empty($project['demo_url'])): ?>
                                    <a href="<?= escape((string) $project['demo_url']) ?>" target="_blank" rel="noopener">Live demo</a>
                                <?php endif; ?>
                                <?php if (!empty($project['repo_url'])): ?>
                                    <a href="<?= escape((string) $project['repo_url']) ?>" target="_blank" rel="noopener">Repository</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($gallery !== []): ?>
                            <div class="public-media">
                                <?php foreach ($gallery as $item): ?>
                                    <?php
                                    $itemUrl = appUrl('/assets/uploads/' . rawurlencode((string) $item['stored_name']));
                                    $itemType = (string) $item['mime_type'];
                                    $itemName = (string) $item['original_name'];
                                    ?>
                                    <?php if (str_starts_with($itemType, 'video/')): ?>
                                        <video class="public-media-video" controls preload="metadata">
                                            <source src="<?= escape($itemUrl) ?>" type="<?= escape($itemType) ?>">
                                        </video>
                                    <?php elseif (str_starts_with($itemType, 'image/')): ?>
                                        <button
                                            type="button"
                                            class="public-media-thumb"
                                            data-lightbox-src="<?= escape($itemUrl) ?>"
                                            data-lightbox-label="<?= escape($itemName) ?>"
                                            aria-label="Perbesar <?= escape($itemName) ?>"
                                        >
                                            <img src="<?= escape($itemUrl) ?>" alt="<?= escape($itemName) ?>" loading="lazy">
                                        </button>
                                    <?php else: ?>
                                        <a class="public-media-pdf" href="<?= escape($itemUrl) ?>" target="_blank" rel="noopener">PDF</a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
