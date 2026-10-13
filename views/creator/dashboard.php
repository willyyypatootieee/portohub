<?php require __DIR__ . '/layout.php'; ?>

<?php
/*
 * Dashboard creator: ringkasan angka, banner progres profil, grafik aktivitas
 * sederhana, dan daftar proyek terbaru. Semua data berasal dari repository.
 */
$firstName = explode(' ', (string) $user['display_name'])[0];
$completion = min(100, 50 + ($counts['total'] * 10));
$mediaCount = count($studioRepository->getMedia($userId));
?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Creator studio</p>
        <h1>Good morning, <?= escape($firstName) ?>.</h1>
        <p>Here’s how your portfolio is taking shape.</p>
    </div>

    <span class="live-status"><i></i> Portfolio is live</span>
</section>

<section class="completion-banner">
    <strong><?= $completion ?>%</strong>

    <div>
        <h2>Your portfolio is almost there</h2>
        <p>Add projects and media to give visitors a reason to stay.</p>
        <a href="<?= escape(appUrl('/studio/projects.php#project-form')) ?>">Complete your profile →</a>
    </div>

    <span><?= (int) $counts['total'] ?> projects</span>
</section>

<section class="metric-grid">
    <article>
        <p>Published projects</p>
        <strong><?= (int) $counts['published'] ?></strong>
        <small>Ready for your public portfolio</small>
    </article>

    <article>
        <p>Projects in progress</p>
        <strong><?= (int) $counts['drafts'] ?></strong>
        <small>Drafts you can continue editing</small>
    </article>

    <article>
        <p>Media assets</p>
        <strong><?= $mediaCount ?></strong>
        <small>Images, video, and documents</small>
    </article>
</section>

<section class="two-column">
    <article class="panel">
        <div class="panel-title">
            <div>
                <h2>Portfolio activity</h2>
                <p>Your studio collection at a glance</p>
            </div>
            <a href="<?= escape(appUrl('/studio/analytics.php')) ?>">Last 30 days</a>
        </div>

        <div class="line-chart" aria-label="Illustrative portfolio activity chart">
            <span style="height: 24%"></span>
            <span style="height: 39%"></span>
            <span style="height: 35%"></span>
            <span style="height: 54%"></span>
            <span style="height: 50%"></span>
            <span style="height: 70%"></span>
            <span style="height: 64%"></span>
            <span style="height: 79%"></span>
            <span style="height: 73%"></span>
            <span style="height: 91%"></span>
        </div>
    </article>

    <article class="panel">
        <div class="panel-title">
            <div>
                <h2>Recent projects</h2>
                <p>Continue where you left off</p>
            </div>
            <a href="<?= escape(appUrl('/studio/projects.php')) ?>">View all</a>
        </div>

        <?php if ($projects === []): ?>
            <div class="empty-state">
                <strong>Become The One</strong>
                <p>Your first project belongs here.</p>
                <a class="button" href="<?= escape(appUrl('/studio/projects.php#project-form')) ?>">Create project</a>
            </div>
        <?php else: ?>
            <ul class="compact-list">
                <?php foreach ($projects as $project): ?>
                    <li>
                        <span class="project-thumbnail"><?= escape(strtoupper(substr((string) $project['title'], 0, 1))) ?></span>

                        <div>
                            <strong><?= escape($project['title']) ?></strong>
                            <small><?= escape($project['category']) ?> · <?= escape($project['status']) ?></small>
                        </div>

                        <a href="<?= escape(appUrl('/studio/projects.php?edit=' . (int) $project['id'] . '#project-form')) ?>">Edit</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
