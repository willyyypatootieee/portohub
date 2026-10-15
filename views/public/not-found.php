<?php
/**
 * Pesan halaman/karya tidak ditemukan. Dipakai route publik (mis. detail
 * proyek) saat data yang diminta tidak ada atau belum dipublikasikan.
 *
 * @var string $noticeTitle
 * @var string $noticeBody
 */
?>
<section class="public-notice" id="top">
    <div class="container">
        <p class="eyebrow">404</p>
        <h1><?= escape($noticeTitle ?? 'Not found') ?></h1>
        <p class="public-notice-text"><?= escape($noticeBody ?? 'Halaman yang Anda cari tidak tersedia.') ?></p>
        <div class="public-notice-actions">
            <a class="button" href="<?= escape(appUrl('/explore/')) ?>">Explore work <span aria-hidden="true">→</span></a>
            <a class="button button-secondary" href="<?= escape(appUrl('/')) ?>">Back home</a>
        </div>
    </div>
</section>
