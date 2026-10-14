<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($creator['display_name']) ?> — Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/public.css')) ?>">
</head>
<body class="public-portfolio">
    <header>
        <a class="studio-brand" href="<?= escape(appUrl('/')) ?>">▮▮ <?= escape(SITE_NAME) ?></a>
        <a href="<?= escape(appUrl('/')) ?>">Explore</a>
    </header>

    <main>
        <p class="eyebrow">Independent creative</p>
        <h1><?= escape($creator['display_name']) ?></h1>
        <h2><?= escape($creator['headline']) ?></h2>
        <p class="portfolio-bio"><?= nl2br(escape($creator['bio'])) ?></p>

        <section>
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
                        ?>
                        <article>
                            <div class="public-thumb">
                                <?php if ($coverUrl !== null): ?>
                                    <button
                                        type="button"
                                        class="public-thumb-button"
                                        data-lightbox-src="<?= escape($coverUrl) ?>"
                                        data-lightbox-label="<?= escape($project['title']) ?>"
                                        aria-label="Perbesar <?= escape($project['title']) ?>"
                                    >
                                        <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?>" loading="lazy">
                                    </button>
                                <?php else: ?>
                                    <span><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                                <?php endif; ?>
                            </div>
                            <p><?= escape($project['category']) ?></p>
                            <h3><?= escape($project['title']) ?></h3>
                            <span><?= nl2br(escape($project['description'])) ?></span>

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
        </section>
    </main>
    <script src="<?= escape(appUrl('/assets/js/lightbox.js')) ?>"></script>
</body>
</html>
