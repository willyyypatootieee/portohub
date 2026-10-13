<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Superadmin monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/studio.css')) ?>">
</head>
<body class="superadmin-page">
    <header>
        <a class="studio-brand" href="<?= escape(appUrl('/')) ?>">▮▮ <?= escape(SITE_NAME) ?></a>
        <nav>
            <a href="<?= escape(appUrl('/')) ?>">View discovery</a>
            <a href="<?= escape(appUrl('/login/superadmin/?logout=1')) ?>">Sign out</a>
        </nav>
    </header>

    <main>
        <p class="eyebrow">Platform monitoring</p>
        <h1>Superadmin overview</h1>
        <p class="admin-description">
            A separate monitoring screen — creator studio navigation is intentionally not included here.
        </p>

        <section class="metric-grid">
            <article>
                <p>Registered creators</p>
                <strong><?= $counts['creators'] ?></strong>
            </article>
            <article>
                <p>Creator projects</p>
                <strong><?= $counts['projects'] ?></strong>
            </article>
            <article>
                <p>Published projects</p>
                <strong><?= $counts['published'] ?></strong>
            </article>
            <article>
                <p>Uploaded assets</p>
                <strong><?= $counts['assets'] ?></strong>
            </article>
        </section>

        <section class="panel">
            <div class="panel-title">
                <div>
                    <h2>Creator activity</h2>
                    <p>Recent creator records and project activity.</p>
                </div>
            </div>

            <?php if ($activity === []): ?>
                <div class="empty-state">
                    <strong>Become The One</strong>
                    <p>No registered creators yet.</p>
                </div>
            <?php else: ?>
                <div class="project-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Creator</th>
                                <th>Email</th>
                                <th>Projects</th>
                                <th>Last activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activity as $creator): ?>
                                <tr>
                                    <td><strong><?= escape($creator['display_name']) ?></strong></td>
                                    <td><?= escape($creator['email']) ?></td>
                                    <td><?= (int) $creator['project_count'] ?></td>
                                    <td><?= escape($creator['last_activity'] ?? 'No projects yet') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
