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

        <?php if (!empty($hasCv)): ?>
            <div class="portfolio-actions">
                <?php /* Tombol CV: membuka flipbook, bukan mengunduh. */ ?>
                <button
                    class="public-cv-cta"
                    type="button"
                    data-cv-open
                    data-cv-src="<?= escape($cvUrl) ?>"
                    data-cv-title="<?= escape($cvTitle) ?>"
                    data-cv-download="<?= escape($cvDownloadUrl) ?>"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H14l6 6v9.5a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18.5Z"/>
                        <path d="M14 3v6h6"/>
                        <path d="M8.5 13.5h7M8.5 16.5h4.5"/>
                    </svg>
                    Lihat CV
                </button>
                <a class="public-cv-download" href="<?= escape($cvDownloadUrl) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3v12"/>
                        <path d="m7.5 10.5 4.5 4.5 4.5-4.5"/>
                        <path d="M4.5 19.5h15"/>
                    </svg>
                    Unduh PDF
                </a>
            </div>
        <?php endif; ?>

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

                            <?php if ($technologies !== []): ?>
                                <span class="public-thumb-tag"><?= escape($technologies[0]) ?></span>
                            <?php endif; ?>

                            <span class="public-thumb-hint">
                                View project
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M5 12h14"/>
                                    <path d="m13 6 6 6-6 6"/>
                                </svg>
                            </span>
                        </a>

                        <div class="public-work-body">
                            <div class="public-work-meta">
                                <p class="public-work-category"><?= escape($project['category']) ?></p>
                                <h3><a href="<?= escape($projectLink) ?>"><?= escape($project['title']) ?></a></h3>
                                <span class="public-work-description"><?= nl2br(escape($project['description'])) ?></span>
                            </div>

                            <?php if (count($technologies) > 1): ?>
                                <ul class="public-tech-chips" aria-label="Teknologi yang digunakan">
                                    <?php foreach (array_slice($technologies, 1, 3) as $technology): ?>
                                        <li><?= escape($technology) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <div class="public-work-foot">
                                <span class="public-work-count">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <rect x="3" y="4.5" width="18" height="15" rx="2"/>
                                        <path d="M3 9.5h18"/>
                                        <path d="m8 15 2-2 2.4 2.4L17 12"/>
                                    </svg>
                                    <?= count($media) ?> file<?= count($media) === 1 ? '' : 's' ?>
                                </span>

                                <div class="public-project-links">
                                    <?php if (!empty($project['demo_url'])): ?>
                                        <a href="<?= escape((string) $project['demo_url']) ?>" target="_blank" rel="noopener">Live demo</a>
                                    <?php endif; ?>
                                    <?php if (!empty($project['repo_url'])): ?>
                                        <a href="<?= escape((string) $project['repo_url']) ?>" target="_blank" rel="noopener">Repository</a>
                                    <?php endif; ?>
                                    <a class="public-work-open" href="<?= escape($projectLink) ?>">
                                        View project
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 12h14"/>
                                            <path d="m13 6 6 6-6 6"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if ($gallery !== []): ?>
                            <div class="public-media">
                                <?php foreach (array_slice($gallery, 0, 4) as $item): ?>
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
                                        <a class="public-media-pdf" href="<?= escape($itemUrl) ?>" target="_blank" rel="noopener">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H14l6 6v9.5a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18.5Z"/>
                                                <path d="M14 3v6h6"/>
                                            </svg>
                                            PDF
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (count($gallery) > 4): ?>
                                    <a class="public-media-more" href="<?= escape($projectLink) ?>">
                                        +<?= count($gallery) - 4 ?> more
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
