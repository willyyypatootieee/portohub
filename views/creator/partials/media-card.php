<?php
/**
 * Kartu satu berkas media. Dipakai panel media proyek dan galeri media di
 * halaman Projects supaya markup dan tombol hapus konsisten.
 *
 * @var array<string, mixed> $asset           Baris media_assets.
 * @var array<string, scalar> $mediaFormFields Field hidden untuk form hapus (action, id, opsional project_id).
 * @var bool $mediaShowProject                 Tampilkan nama proyek induk.
 */
$mediaUrl = appUrl('/assets/uploads/' . rawurlencode((string) $asset['stored_name']));
$mediaType = (string) $asset['mime_type'];
$mediaName = (string) $asset['original_name'];
$mediaKind = str_starts_with($mediaType, 'image/')
    ? 'image'
    : (str_starts_with($mediaType, 'video/') ? 'video' : 'file');
?>
<article class="media-card media-card--<?= $mediaKind ?>">
    <div class="media-card-preview">
        <?php if (str_starts_with($mediaType, 'image/')): ?>
            <button
                type="button"
                class="media-thumb"
                data-lightbox-src="<?= escape($mediaUrl) ?>"
                data-lightbox-type="image"
                data-lightbox-label="<?= escape($mediaName) ?>"
                aria-label="Perbesar <?= escape($mediaName) ?>"
            >
                <img src="<?= escape($mediaUrl) ?>" alt="<?= escape($mediaName) ?>" loading="lazy">
                <?php if ($mediaType === 'image/gif'): ?>
                    <span class="media-badge">GIF</span>
                <?php endif; ?>
            </button>
        <?php elseif (str_starts_with($mediaType, 'video/')): ?>
            <video controls preload="metadata">
                <source src="<?= escape($mediaUrl) ?>" type="<?= escape($mediaType) ?>">
            </video>
            <span class="media-badge">Video</span>
        <?php else: ?>
            <a class="pdf-card" href="<?= escape($mediaUrl) ?>" target="_blank" rel="noopener">
                PDF
                <small>Buka dokumen</small>
            </a>
        <?php endif; ?>
    </div>

    <div class="media-card-info">
        <strong title="<?= escape($mediaName) ?>"><?= escape($mediaName) ?></strong>
        <small>
            <?php if (!empty($mediaShowProject)): ?>
                <?= escape((string) ($asset['project_title'] ?? '') !== '' ? (string) $asset['project_title'] : 'Tanpa proyek') ?> ·
            <?php endif; ?>
            <?= number_format((int) $asset['file_size'] / 1024, 1) ?> KB
        </small>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
            <?php foreach ($mediaFormFields as $fieldName => $fieldValue): ?>
                <input type="hidden" name="<?= escape((string) $fieldName) ?>" value="<?= escape((string) $fieldValue) ?>">
            <?php endforeach; ?>
            <button type="submit">Delete</button>
        </form>
    </div>
</article>
