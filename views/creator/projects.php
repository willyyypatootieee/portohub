<?php require __DIR__ . '/layout.php'; ?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Project library</p>
        <h1>The work, organized.</h1>
        <p>Draft, publish, feature, and archive your case studies.</p>
    </div>

    <a class="button" href="#project-form">＋ New project</a>
</section>

<section class="panel project-editor" id="project-form">
    <div class="panel-title">
        <div>
            <h2><?= $editingProject ? 'Edit project' : 'Start a new project' ?></h2>
            <p>Semua field wajib divalidasi di server dari JSON schema proyek.</p>
        </div>

        <?php if ($editingProject): ?>
            <a href="<?= escape(appUrl('/studio/projects.php')) ?>">Cancel</a>
        <?php endif; ?>
    </div>

    <form method="post" enctype="multipart/form-data" class="studio-form">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) ($editingProject['id'] ?? 0) ?>">

        <label>
            Project title
            <input name="title" value="<?= escape($editingProject['title'] ?? '') ?>" required>
            <?php if (isset($errors['title'])): ?><small><?= escape($errors['title']) ?></small><?php endif; ?>
        </label>

        <label>
            Category
            <input name="category" value="<?= escape($editingProject['category'] ?? '') ?>" placeholder="Branding, UI/UX, Photography…" required>
            <?php if (isset($errors['category'])): ?><small><?= escape($errors['category']) ?></small><?php endif; ?>
        </label>

        <label>
            Status
            <select name="status">
                <option value="draft" <?= ($editingProject['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($editingProject['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="archived" <?= ($editingProject['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
            <?php if (isset($errors['status'])): ?><small><?= escape($errors['status']) ?></small><?php endif; ?>
        </label>

        <label class="check-field">
            <input type="checkbox" name="featured" value="1" <?= !empty($editingProject['featured']) ? 'checked' : '' ?>>
            Feature on my portfolio
        </label>

        <label class="full-field">
            Description
            <textarea name="description" required><?= escape($editingProject['description'] ?? '') ?></textarea>
            <?php if (isset($errors['description'])): ?><small><?= escape($errors['description']) ?></small><?php endif; ?>
        </label>

        <?php if (!$editingProject): ?>
            <label class="full-field media-field">
                <strong>Media proyek (opsional)</strong>
                <span>JPG, PNG, WebP, GIF, MP4, WebM, atau PDF — maksimal 25 MB per berkas. Bisa pilih beberapa sekaligus. Gambar pertama menjadi cover.</span>
                <input
                    type="file"
                    name="portfolio_files[]"
                    accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,application/pdf"
                    multiple
                >
            </label>
        <?php endif; ?>

        <div class="full-field">
            <button class="button" type="submit"><?= $editingProject ? 'Save project' : 'Save project &amp; upload' ?></button>
        </div>
    </form>
</section>

<?php if ($editingProject && (int) ($editingProject['id'] ?? 0) > 0): ?>
    <?php
    /*
     * Panel media menyatu dengan edit proyek. Upload di sini otomatis
     * terhubung ke proyek yang sedang dibuka. Semua berkas creator juga
     * tampil di galeri media di bagian bawah halaman ini.
     */
    $currentProjectId = (int) $editingProject['id'];
    ?>
    <section class="panel project-media" id="project-media">
        <div class="panel-title">
            <div>
                <h2>Project media</h2>
                <p>Unggah gambar, GIF, video, atau PDF langsung ke proyek ini. Gambar pertama menjadi cover.</p>
            </div>
        </div>

        <?php foreach ($mediaErrors as $mediaError): ?>
            <p class="studio-alert error" role="alert"><?= escape($mediaError) ?></p>
        <?php endforeach; ?>

        <form method="post" enctype="multipart/form-data" class="upload-form project-upload-form">
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
            <input type="hidden" name="action" value="upload-media">
            <input type="hidden" name="project_id" value="<?= $currentProjectId ?>">

            <label>
                <strong>Tambahkan berkas ke proyek</strong>
                <span>JPG, PNG, WebP, GIF, MP4, WebM, atau PDF — maksimal 25 MB per berkas. Bisa pilih beberapa sekaligus.</span>
                <input
                    type="file"
                    name="portfolio_files[]"
                    accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,application/pdf"
                    multiple
                    required
                >
            </label>

            <button class="button" type="submit">Upload ke proyek</button>
        </form>

        <?php if ($projectMedia === []): ?>
            <div class="empty-state">
                <strong>Become The One</strong>
                <p>Belum ada media di proyek ini. Unggah karya pertamamu di atas.</p>
            </div>
        <?php else: ?>
            <div class="media-grid">
                <?php foreach ($projectMedia as $asset): ?>
                    <?php
                    $mediaFormFields = [
                        'action' => 'delete-media',
                        'id' => (int) $asset['id'],
                        'project_id' => $currentProjectId,
                    ];
                    $mediaShowProject = false;
                    require __DIR__ . '/partials/media-card.php';
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="panel">
    <div class="project-controls">
        <nav class="filter-tabs">
            <?php
            $statusTabs = [
                'all' => ['label' => 'All', 'count' => $counts['total']],
                'published' => ['label' => 'Published', 'count' => $counts['published']],
                'draft' => ['label' => 'Drafts', 'count' => $counts['drafts']],
                'archived' => ['label' => 'Archived', 'count' => $counts['archived']],
            ];
            ?>

            <?php foreach ($statusTabs as $key => $tab): ?>
                <a
                    class="<?= $status === $key ? 'is-active' : '' ?>"
                    href="<?= escape(appUrl('/studio/projects.php?status=' . $key)) ?>"
                >
                    <?= escape($tab['label']) ?> <?= (int) $tab['count'] ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="get" class="inline-search">
            <input type="hidden" name="status" value="<?= escape($status) ?>">
            <input type="search" name="q" value="<?= escape($search) ?>" placeholder="Search projects">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if ($projects === []): ?>
        <div class="empty-state">
            <strong>Become The One</strong>
            <p>No projects match this view yet.</p>
        </div>
    <?php else: ?>
        <div class="project-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Assets</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <strong><?= escape($project['title']) ?></strong>
                                <small><?= escape(mb_strimwidth((string) $project['description'], 0, 70, '…')) ?></small>
                            </td>
                            <td><?= escape($project['category']) ?></td>
                            <td><span class="status <?= escape($project['status']) ?>"><?= escape($project['status']) ?></span></td>
                            <td><?= (int) $project['media_count'] ?></td>
                            <td class="row-actions">
                                <a href="<?= escape(appUrl('/studio/projects.php?edit=' . (int) $project['id'] . '#project-media')) ?>">Edit &amp; media</a>
                                <form method="post" onsubmit="return confirm('Delete this project and its media links?');">
                                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="panel" id="media-gallery">
    <div class="panel-title">
        <div>
            <h2>Media gallery</h2>
            <p>Semua gambar, GIF, video, dan PDF yang terhubung ke proyek Anda. Tambah berkas saat membuat proyek, atau buka proyek lalu unggah dari panel Project media.</p>
        </div>
    </div>

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
                    class="<?= $mediaFilter === $key ? 'is-active' : '' ?>"
                    href="<?= escape(appUrl('/studio/projects.php?media=' . $key)) ?>#media-gallery"
                >
                    <?= escape($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <?php if ($galleryMedia === []): ?>
        <div class="empty-state">
            <strong>Become The One</strong>
            <p>Belum ada media. Buka atau buat proyek untuk mengunggah gambar, GIF, video, atau PDF.</p>
        </div>
    <?php else: ?>
        <div class="media-grid">
            <?php foreach ($galleryMedia as $asset): ?>
                <?php
                $mediaFormFields = [
                    'action' => 'delete-gallery',
                    'id' => (int) $asset['id'],
                ];
                $mediaShowProject = true;
                require __DIR__ . '/partials/media-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
