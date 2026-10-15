<?php require __DIR__ . '/layout.php'; ?>

<?php
/*
 * Dashboard creator: status publikasi, langkah penyelesaian profil, dan daftar
 * proyek terbaru. Semua angka berasal dari repository untuk pengguna yang
 * sedang masuk — tidak ada metrik contoh atau grafik dekoratif.
 */
$firstName = explode(' ', (string) $user['display_name'])[0];
$mediaCount = count($studioRepository->getMedia($userId));

$hasCv = !empty($user['cv_filename']);
$checklist = [
    [
        'label' => 'Isi headline & bio',
        'done' => trim((string) $user['headline']) !== '' && trim((string) $user['bio']) !== '',
        'url' => '/studio/settings.php#public-profile',
    ],
    [
        'label' => 'Unggah CV',
        'done' => $hasCv,
        'url' => '/studio/settings.php#cv',
    ],
    [
        'label' => 'Buat proyek pertama',
        'done' => (int) $counts['total'] > 0,
        'url' => '/studio/projects.php#project-form',
    ],
    [
        'label' => 'Terbitkan satu proyek',
        'done' => (int) $counts['published'] > 0,
        'url' => '/studio/projects.php',
    ],
];

$doneCount = count(array_filter($checklist, fn (array $item): bool => $item['done']));
$completion = (int) round(($doneCount / count($checklist)) * 100);
$remaining = count($checklist) - $doneCount;

$statusLabels = ['published' => 'Published', 'draft' => 'Draft', 'archived' => 'Archived'];
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
        <?php if ($remaining === 0): ?>
            <h2>Your portfolio is complete</h2>
            <p>Everything is set — keep adding work whenever you’re ready.</p>
            <a href="<?= escape(appUrl('/studio/projects.php#project-form')) ?>">
                Add another project
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </a>
        <?php else: ?>
            <h2>Your portfolio is almost there</h2>
            <p><?= $remaining ?> step<?= $remaining === 1 ? '' : 's' ?> left to give visitors a reason to stay.</p>
            <a href="<?= escape(appUrl($checklist[array_key_first(array_filter($checklist, fn (array $i): bool => !$i['done']))]['url'])) ?>">
                Continue setup
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </a>
        <?php endif; ?>
    </div>

    <span><?= $doneCount ?> / <?= count($checklist) ?> done</span>
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
                <h2>Finish your portfolio</h2>
                <p>Complete these steps to look your best</p>
            </div>
            <span class="panel-badge <?= $remaining === 0 ? '' : 'is-warning' ?>">
                <?= $completion ?>%
            </span>
        </div>

        <ul class="setup-checklist">
            <?php foreach ($checklist as $item): ?>
                <li class="<?= $item['done'] ? 'is-done' : '' ?>">
                    <span class="setup-mark" aria-hidden="true">
                        <?php if ($item['done']): ?>
                            <svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
                        <?php else: ?>
                            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        <?php endif; ?>
                    </span>
                    <span class="setup-label">
                        <?= escape($item['label']) ?>
                        <small><?= $item['done'] ? 'Done' : 'Not yet' ?></small>
                    </span>
                    <?php if (!$item['done']): ?>
                        <a href="<?= escape(appUrl($item['url'])) ?>">
                            Start
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
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
                            <small>
                                <?= escape($project['category']) ?>
                                <span class="status-dot is-<?= escape((string) $project['status']) ?>"></span>
                                <?= escape($statusLabels[$project['status']] ?? ucfirst((string) $project['status'])) ?>
                            </small>
                        </div>

                        <a href="<?= escape(appUrl('/studio/projects.php?edit=' . (int) $project['id'] . '#project-form')) ?>">Edit</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
