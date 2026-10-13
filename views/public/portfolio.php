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
                        ?>
                        <article>
                            <div class="public-thumb">
                                <?php if ($coverUrl !== null): ?>
                                    <img src="<?= escape($coverUrl) ?>" alt="<?= escape($project['title']) ?>">
                                <?php else: ?>
                                    <span><?= escape(strtoupper(mb_substr((string) $project['title'], 0, 1))) ?></span>
                                <?php endif; ?>
                            </div>
                            <p><?= escape($project['category']) ?></p>
                            <h3><?= escape($project['title']) ?></h3>
                            <span><?= nl2br(escape($project['description'])) ?></span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
