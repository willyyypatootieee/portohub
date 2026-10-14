<?php require __DIR__ . '/layout.php'; ?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Account control</p>
        <h1>Make it yours.</h1>
        <p>Update the public identity visitors see on your portfolio.</p>
    </div>
</section>

<section class="settings-layout">
    <nav class="settings-nav">
        <a class="is-active" href="#public-profile">Public profile</a>
        <a href="#cv">CV / documents</a>
        <a href="<?= escape(appUrl('/studio/logout.php')) ?>">Security / sign out</a>
    </nav>

    <form method="post" class="panel studio-form settings-form" id="public-profile">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="profile">

        <div class="panel-title">
            <div>
                <h2>Basic information</h2>
                <p>Tell visitors who you are and what you do.</p>
            </div>
        </div>

        <label>
            Display name
            <input name="display_name" value="<?= escape($user['display_name']) ?>" required>
            <?php if (isset($errors['display_name'])): ?>
                <small><?= escape($errors['display_name']) ?></small>
            <?php endif; ?>
        </label>

        <label>
            Portfolio username
            <input name="username" value="<?= escape($user['username']) ?>" required>
            <?php if (isset($errors['username'])): ?>
                <small><?= escape($errors['username']) ?></small>
            <?php endif; ?>
            <small>
                Your public address:
                <?= escape(appUrl('/portfolio/?user=')) ?><span data-username-preview><?= escape($user['username']) ?></span>
            </small>
        </label>

        <label class="full-field">
            Professional headline
            <input name="headline" value="<?= escape($user['headline']) ?>" required>
            <?php if (isset($errors['headline'])): ?>
                <small><?= escape($errors['headline']) ?></small>
            <?php endif; ?>
        </label>

        <label class="full-field">
            Bio
            <textarea name="bio" required><?= escape($user['bio']) ?></textarea>
            <?php if (isset($errors['bio'])): ?>
                <small><?= escape($errors['bio']) ?></small>
            <?php endif; ?>
        </label>

        <div class="full-field">
            <button class="button" type="submit">Save changes</button>
        </div>
    </form>
</section>

<section class="panel cv-panel" id="cv">
    <div class="panel-title">
        <div>
            <h2>CV / documents</h2>
            <p>Unggah atau ganti CV berformat PDF. Berkas disimpan di luar web root, jadi hanya Anda yang bisa melihat dan mengunduhnya.</p>
        </div>
    </div>

    <?php if ($cvError !== null): ?>
        <p class="studio-alert error" role="alert"><?= escape($cvError) ?></p>
    <?php endif; ?>

    <?php if (!empty($user['cv_filename'])): ?>
        <div class="cv-card">
            <span class="cv-thumb" aria-hidden="true">PDF</span>

            <div class="cv-meta">
                <strong title="<?= escape((string) ($user['cv_original_name'] ?? '')) ?>">
                    <?= escape((string) ($user['cv_original_name'] ?? '') !== '' ? (string) $user['cv_original_name'] : 'Curriculum vitae.pdf') ?>
                </strong>
                <small>Diunggah <?= escape(date('d M Y', strtotime((string) $user['cv_uploaded_at']))) ?></small>
            </div>

            <div class="cv-actions">
                <a class="button button-outline" href="<?= escape(appUrl('/studio/cv.php')) ?>" target="_blank" rel="noopener">Preview</a>
                <a class="button button-outline" href="<?= escape(appUrl('/studio/cv.php?download=1')) ?>">Download</a>

                <form method="post" onsubmit="return confirm('Hapus CV ini? Anda bisa mengunggahnya lagi kapan saja.');">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                    <input type="hidden" name="action" value="delete-cv">
                    <button class="cv-delete" type="submit">Delete</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="cv-empty">Belum ada CV. Unggah file PDF agar perekrut bisa melihat riwayat singkat Anda.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="cv-upload">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="upload-cv">

        <label>
            <strong><?= !empty($user['cv_filename']) ? 'Ganti file CV' : 'Unggah CV' ?></strong>
            <span>Format PDF, maksimal 10 MB. Jenis dan ukuran berkas diverifikasi di server.</span>
            <input type="file" name="cv_file" accept="application/pdf" required>
        </label>

        <button class="button" type="submit"><?= !empty($user['cv_filename']) ? 'Ganti CV' : 'Upload CV' ?></button>
    </form>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
