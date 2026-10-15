<?php
/**
 * Konten halaman detail proyek publik. Dibungkus layout header/footer.
 *
 * @var array<string, mixed> $project
 * @var array<int, array<string, mixed>> $media
 * @var array<int, array<string, mixed>> $moreProjects
 * @var int $creatorProjectCount
 */

$portfolioLink = appUrl('/portfolio/' . rawurlencode((string) $project['username']) . '/');

$cover = $project['cover'] ?? null;
$coverUrl = $cover !== null
    ? appUrl('/assets/uploads/' . rawurlencode((string) $cover['stored_name']))
    : null;
$coverStored = (string) ($cover['stored_name'] ?? '');

// Galeri = semua media kecuali gambar yang sudah tampil sebagai cover.
$gallery = array_values(array_filter(
    $media,
    static fn (array $item): bool => (string) ($item['stored_name'] ?? '') !== $coverStored
));

$creatorInitial = mb_strtoupper(mb_substr((string) $project['display_name'], 0, 1));
?>
<article class="project-detail" id="top">
    <div class="container">
        <nav class="project-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= escape(appUrl('/explore/')) ?>">Explore</a>
            <span aria-hidden="true">/</span>
            <a href="<?= escape($portfolioLink) ?>"><?= escape($project['display_name']) ?></a>
        </nav>

        <header class="project-head">
            <p class="eyebrow"><?= escape($project['category']) ?></p>
            <h1><?= escape($project['title']) ?></h1>
            <a class="project-creator" href="<?= escape($portfolioLink) ?>">
                <span class="creator-badge" aria-hidden="true"><?= escape($creatorInitial) ?></span>
                <span>
                    <strong><?= escape($project['display_name']) ?></strong>
                    <?php if (trim((string) $project['headline']) !== ''): ?>
                        <small><?= escape($project['headline']) ?></small>
                    <?php endif; ?>
                </span>
            </a>
        </header>

        <figure class="project-cover">
            <?php if ($coverUrl !== null): ?>
                <button
                    type="button"
                    class="project-cover-button"
                    data-lightbox-src="<?= escape($coverUrl) ?>"
                    data-lightbox-label="<?= escape($project['title']) ?>"
                    aria-label="Perbesar <?= escape($project['title']) ?>"
                >
                    <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?> by <?= escape($project['display_name']) ?>">
                </button>
            <?php else: ?>
                <span class="project-cover-placeholder"><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
            <?php endif; ?>
        </figure>

        <div class="project-body">
            <div class="project-description">
                <h2>About this project</h2>
                <p><?= nl2br(escape($project['description'])) ?></p>
            </div>

            <aside class="project-facts">
                <dl>
                    <div>
                        <dt>Category</dt>
                        <dd><?= escape($project['category']) ?></dd>
                    </div>
                    <div>
                        <dt>Creator</dt>
                        <dd><a href="<?= escape($portfolioLink) ?>"><?= escape($project['display_name']) ?></a></dd>
                    </div>
                    <div>
                        <dt>Files</dt>
                        <dd><?= count($media) ?></dd>
                    </div>
                    <div>
                        <dt>Published works</dt>
                        <dd><?= (int) $creatorProjectCount ?></dd>
                    </div>
                </dl>
                <a class="button button-secondary project-facts-action" href="<?= escape($portfolioLink) ?>">
                    View full portfolio
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m13 6 6 6-6 6"/>
                    </svg>
                </a>
            </aside>
        </div>

        <?php if ($gallery !== []): ?>
            <section class="project-gallery">
                <div class="project-section-heading">
                    <h2>Gallery</h2>
                    <span><?= count($gallery) ?> item<?= count($gallery) === 1 ? '' : 's' ?></span>
                </div>

                <div class="project-gallery-grid">
                    <?php foreach ($gallery as $item): ?>
                        <?php
                        $itemUrl = appUrl('/assets/uploads/' . rawurlencode((string) $item['stored_name']));
                        $itemType = (string) $item['mime_type'];
                        $itemName = (string) $item['original_name'];
                        ?>
                        <?php if (str_starts_with($itemType, 'video/')): ?>
                            <figure class="project-media project-media-video">
                                <video controls preload="metadata">
                                    <source src="<?= escape($itemUrl) ?>" type="<?= escape($itemType) ?>">
                                </video>
                                <figcaption><?= escape($itemName) ?></figcaption>
                            </figure>
                        <?php elseif (str_starts_with($itemType, 'image/')): ?>
                            <button
                                type="button"
                                class="project-media project-media-image"
                                data-lightbox-src="<?= escape($itemUrl) ?>"
                                data-lightbox-label="<?= escape($itemName) ?>"
                                aria-label="Perbesar <?= escape($itemName) ?>"
                            >
                                <img src="<?= escape($itemUrl) ?>" alt="<?= escape($itemName) ?>" loading="lazy">
                            </button>
                        <?php else: ?>
                            <a class="project-media project-media-file" href="<?= escape($itemUrl) ?>" target="_blank" rel="noopener">
                                <span class="project-media-file-tag">PDF</span>
                                <span class="project-media-file-name"><?= escape($itemName) ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($moreProjects !== []): ?>
            <section class="project-more">
                <div class="project-section-heading">
                    <h2>More from <?= escape($project['display_name']) ?></h2>
                    <a class="project-more-link" href="<?= escape($portfolioLink) ?>">
                        View all
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 12h14"/>
                            <path d="m13 6 6 6-6 6"/>
                        </svg>
                    </a>
                </div>

                <div class="portfolio-grid">
                    <?php foreach ($moreProjects as $item): ?>
                        <?php
                        $itemCover = $item['cover'] ?? null;
                        $itemCoverUrl = $itemCover !== null
                            ? appUrl('/assets/uploads/' . rawurlencode((string) $itemCover['stored_name']))
                            : null;
                        $itemLink = appUrl('/project/' . (int) $item['id']);
                        ?>
                        <article class="work-card">
                            <div class="work-image">
                                <a class="work-cover-link" href="<?= escape($itemLink) ?>" aria-label="View <?= escape($item['title']) ?>">
                                    <?php if ($itemCoverUrl !== null): ?>
                                        <img src="<?= escape($itemCoverUrl) ?>" alt="<?= escape($item['title']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <span class="work-placeholder"><?= escape(strtoupper(mb_substr((string) $item['title'], 0, 1))) ?></span>
                                    <?php endif; ?>
                                </a>
                                <span class="work-category"><?= escape($item['category']) ?></span>
                            </div>
                            <div class="work-details">
                                <div>
                                    <h3><a href="<?= escape($itemLink) ?>"><?= escape($item['title']) ?></a></h3>
                                    <p><?= (int) $item['media_count'] ?> file<?= (int) $item['media_count'] === 1 ? '' : 's' ?></p>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</article>
