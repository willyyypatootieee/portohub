<?php require __DIR__ . '/layout.php'; ?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Asset library</p>
        <h1>Everything you’ve made.</h1>
        <p>Keep your images, films, PDFs, and experiments in one place.</p>
    </div>

    <a class="button" href="#upload-media">＋ Upload media</a>
</section>

<?php if ($uploadError): ?>
    <p class="studio-alert error" role="alert">
        <?= escape($uploadError) ?>
    </p>
<?php endif; ?>

<section class="upload-panel" id="upload-media">
    <form method="post" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="upload">

        <label>
            <strong>Drop your portfolio file here</strong>
            <span>JPG, PNG, WebP, GIF, MP4, WebM, atau PDF — maksimal 25 MB. MIME type diperiksa di server.</span>
            <input
                type="file"
                name="portfolio_file"
                accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,application/pdf"
                required
            >
        </label>

        <label>
            Attach to a project (optional)
            <select name="project_id">
                <option value="">Unassigned media</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?= (int) $project['id'] ?>">
                        <?= escape($project['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button class="button" type="submit">Upload file</button>
    </form>
</section>

<section class="panel">
    <div class="project-controls">
        <nav class="filter-tabs" aria-label="Filter media">
            <?php
            $mediaFilters = [
                'all' => 'All files',
                'images' => 'Images',
                'video' => 'Video',
                'documents' => 'Documents',
            ];
            ?>

            <?php foreach ($mediaFilters as $key => $label): ?>
                <a
                    class="<?= $filter === $key ? 'is-active' : '' ?>"
                    href="<?= escape(appUrl('/studio/media.php?type=' . $key)) ?>"
                >
                    <?= escape($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <?php if ($media === []): ?>
        <div class="empty-state">
            <strong>Become The One</strong>
            <p>Your uploaded work will appear here.</p>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($media as $asset): ?>
                <?php $assetUrl = appUrl('/assets/uploads/' . rawurlencode((string) $asset['stored_name'])); ?>
                <article class="media-card">
                    <?php if (str_starts_with((string) $asset['mime_type'], 'image/')): ?>
                        <img src="<?= escape($assetUrl) ?>" alt="<?= escape($asset['original_name']) ?>">
                    <?php elseif (str_starts_with((string) $asset['mime_type'], 'video/')): ?>
                        <video controls preload="metadata">
                            <source src="<?= escape($assetUrl) ?>" type="<?= escape($asset['mime_type']) ?>">
                        </video>
                    <?php else: ?>
                        <a class="pdf-card" href="<?= escape($assetUrl) ?>" target="_blank" rel="noopener">
                            PDF
                            <small>Open document</small>
                        </a>
                    <?php endif; ?>

                    <div>
                        <strong><?= escape($asset['original_name']) ?></strong>
                        <small>
                            <?= escape($asset['project_title'] ?: 'Unassigned') ?>
                            · <?= number_format((int) $asset['file_size'] / 1024, 1) ?> KB
                        </small>

                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $asset['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
