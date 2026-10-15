<?php require __DIR__ . '/layout.php'; ?>

<?php
/*
 * Profil creator: identitas publik, bio, dan CV.
 *
 * Halaman ini menggabungkan pratinjau identitas (kartu profil) dengan form
 * pengelolaannya, sehingga creator langsung melihat hasil akhirnya. CV
 * ditampilkan lewat flipbook (lihat assets/js/cv-flipbook.js) dan dapat
 * dibuka pengunjung dari halaman portofolio publik.
 */
$hasCv = !empty($user['cv_filename']);
$profileComplete = trim((string) $user['headline']) !== '' && trim((string) $user['bio']) !== '';
?>

<section class="intro-row">
    <div>
        <p class="eyebrow">Profile &amp; CV</p>
        <h1>Your public identity.</h1>
        <p>Shape how visitors meet you — name, headline, bio, and the CV they can flip through.</p>
    </div>

    <a class="button button-outline intro-action" href="<?= escape(appUrl('/portfolio/' . rawurlencode((string) $user['username']) . '/')) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        View public portfolio
    </a>
</section>

<section class="profile-layout">
    <!-- Pratinjau kartu profil: bentuk nyata identitas publik creator. -->
    <aside class="profile-preview" aria-label="Pratinjau profil publik">
        <span class="profile-preview-avatar" aria-hidden="true">
            <?= escape(mb_strtoupper(mb_substr((string) $user['display_name'], 0, 1))) ?>
        </span>
        <strong class="profile-preview-name"><?= escape($user['display_name']) ?></strong>
        <small class="profile-preview-handle">@<?= escape($user['username']) ?></small>
        <p class="profile-preview-headline"><?= escape($user['headline'] !== '' ? $user['headline'] : 'Belum ada headline') ?></p>

        <dl class="profile-preview-stats">
            <div>
                <dt>Projects</dt>
                <dd><?= (int) $counts['published'] ?></dd>
            </div>
            <div>
                <dt>CV</dt>
                <dd class="<?= $hasCv ? 'is-ready' : 'is-missing' ?>">
                    <?= $hasCv ? 'Ready' : 'Missing' ?>
                </dd>
            </div>
        </dl>

        <a class="profile-preview-link" href="<?= escape(appUrl('/portfolio/' . rawurlencode((string) $user['username']) . '/')) ?>" target="_blank" rel="noopener">
            <span>/portfolio/<?= escape($user['username']) ?>/</span>
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 17 17 7"/>
                <path d="M9 7h8v8"/>
            </svg>
        </a>
    </aside>

    <form method="post" class="panel studio-form settings-form" id="public-profile">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="profile">

        <div class="panel-title">
            <div>
                <h2>Public profile</h2>
                <p>Tell visitors who you are and what you do.</p>
            </div>
            <?php if ($profileComplete): ?>
                <span class="panel-badge">Complete</span>
            <?php else: ?>
                <span class="panel-badge is-warning">Needs detail</span>
            <?php endif; ?>
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
                Public address:
                <?= escape(appUrl('/portfolio/' )) ?><span data-username-preview><?= escape($user['username']) ?></span>/
            </small>
        </label>

        <label class="full-field">
            Professional headline
            <input name="headline" value="<?= escape($user['headline']) ?>" placeholder="Mis. UI/UX Designer &amp; Front-end Developer" required>
            <?php if (isset($errors['headline'])): ?>
                <small><?= escape($errors['headline']) ?></small>
            <?php endif; ?>
        </label>

        <label class="full-field">
            Bio
            <textarea name="bio" rows="4" placeholder="Ceritakan singkat tentang keahlian dan cara Anda bekerja." required><?= escape($user['bio']) ?></textarea>
            <?php if (isset($errors['bio'])): ?>
                <small><?= escape($errors['bio']) ?></small>
            <?php endif; ?>
        </label>

        <div class="full-field">
            <button class="button" type="submit">Save profile</button>
        </div>
    </form>
</section>

<section class="panel cv-panel" id="cv">
    <div class="panel-title">
        <div>
            <h2>CV</h2>
            <p>
                Unggah CV berformat PDF. Berkas disimpan di luar web root, jadi hanya Anda yang
                bisa mengunduhnya — pengunjung melihatnya lewat flipbook pada halaman portofolio.
            </p>
        </div>
        <?php if ($hasCv): ?>
            <span class="panel-badge">Published</span>
        <?php endif; ?>
    </div>

    <?php if ($cvError !== null): ?>
        <p class="studio-alert error" role="alert"><?= escape($cvError) ?></p>
    <?php endif; ?>

    <?php if ($hasCv): ?>
        <div class="cv-card">
            <span class="cv-thumb" aria-hidden="true">PDF</span>

            <div class="cv-meta">
                <strong title="<?= escape((string) ($user['cv_original_name'] ?? '')) ?>">
                    <?= escape((string) ($user['cv_original_name'] ?? '') !== '' ? (string) $user['cv_original_name'] : 'Curriculum vitae.pdf') ?>
                </strong>
                <small>Diunggah <?= escape(date('d M Y', strtotime((string) $user['cv_uploaded_at']))) ?></small>
            </div>

            <div class="cv-actions">
                <!-- Flipbook: pengalaman membaca ala buku, bukan sekadar tab PDF. -->
                <button
                    class="button button-outline cv-icon-button"
                    type="button"
                    data-cv-open
                    data-cv-src="<?= escape(appUrl('/studio/cv.php')) ?>"
                    data-cv-title="<?= escape((string) ($user['cv_original_name'] ?? 'Curriculum vitae.pdf')) ?>"
                    data-cv-download="<?= escape(appUrl('/studio/cv.php?download=1')) ?>"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 6.5C10.4 5.2 8.3 4.5 5.5 4.5v13c2.8 0 4.9.7 6.5 2 1.6-1.3 3.7-2 6.5-2v-13c-2.8 0-4.9.7-6.5 2Z"/>
                        <path d="M12 6.5v13"/>
                    </svg>
                    Read flipbook
                </button>

                <a class="button button-outline cv-icon-button" href="<?= escape(appUrl('/studio/cv.php?download=1')) ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3v12"/>
                        <path d="m7.5 10.5 4.5 4.5 4.5-4.5"/>
                        <path d="M4.5 19.5h15"/>
                    </svg>
                    Download
                </a>

                <form method="post" onsubmit="return confirm('Hapus CV ini? Anda bisa mengunggahnya lagi kapan saja.');">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                    <input type="hidden" name="action" value="delete-cv">
                    <button class="cv-delete" type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4.5 6.5h15"/>
                            <path d="M9.5 6.5V4.5h5v2"/>
                            <path d="M6.5 6.5 7.5 20h9l1-13.5"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="cv-empty">Belum ada CV. Unggah file PDF agar pengunjung bisa membacanya lewat flipbook di portofolio Anda.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="cv-upload">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
        <input type="hidden" name="action" value="upload-cv">

        <label class="cv-drop">
            <strong><?= $hasCv ? 'Ganti file CV' : 'Unggah CV' ?></strong>
            <span>Format PDF, maksimal 10 MB. Jenis dan ukuran berkas diverifikasi di server.</span>
            <input type="file" name="cv_file" accept="application/pdf" required>
        </label>

        <button class="button" type="submit"><?= $hasCv ? 'Replace CV' : 'Upload CV' ?></button>
    </form>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
