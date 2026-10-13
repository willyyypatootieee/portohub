<?php require __DIR__ . '/layout.php'; ?>

<?php
/*
 * Halaman analitik: angka ringkas, grafik batang ilustratif, dan daftar
 * langkah berikutnya. Tinggi batang ditulis sebagai daftar agar mudah diubah.
 */
$chartHeights = [32, 48, 41, 60, 55, 73, 62, 80, 66, 88, 75, 100];
?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Audience insights</p>
        <h1>See what resonates.</h1>
        <p>Understand the progress of your portfolio collection.</p>
    </div>
</section>

<section class="notice-banner">
    <strong>↗ Your work is ready to reach more people.</strong>
    <span>Publish your completed drafts to make them visible on your public portfolio.</span>
    <a href="<?= escape(appUrl('/studio/projects.php?status=draft')) ?>">Review drafts</a>
</section>

<section class="metric-grid">
    <article>
        <p>Published projects</p>
        <strong><?= (int) $counts['published'] ?></strong>
        <small>Visible in your portfolio</small>
    </article>

    <article>
        <p>In progress</p>
        <strong><?= (int) $counts['drafts'] ?></strong>
        <small>Drafts waiting for your touch</small>
    </article>

    <article>
        <p>Archived</p>
        <strong><?= (int) $counts['archived'] ?></strong>
        <small>Projects kept off the public page</small>
    </article>
</section>

<section class="two-column">
    <article class="panel">
        <div class="panel-title">
            <div>
                <h2>Portfolio growth</h2>
                <p>Project activity over time</p>
            </div>
        </div>

        <div class="bar-chart">
            <?php foreach ($chartHeights as $height): ?>
                <span style="height: <?= (int) $height ?>%"></span>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="panel">
        <div class="panel-title">
            <div>
                <h2>Next best steps</h2>
                <p>Small actions with a visible impact</p>
            </div>
        </div>

        <ul class="insight-list">
            <li>
                <strong>Create a case study</strong>
                <span>Add the context behind your strongest work.</span>
            </li>
            <li>
                <strong>Publish a draft</strong>
                <span>Move ready work from studio to public portfolio.</span>
            </li>
            <li>
                <strong>Upload a CV or reel</strong>
                <span>Give prospective clients another way to know you.</span>
            </li>
        </ul>
    </article>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
