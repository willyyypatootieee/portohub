<?php
/**
 * Hero landing page.
 *
 * Statistik komunitas, kartu "FEATURED", dan deretan avatar semuanya diisi
 * dari data nyata (lihat public/index.php): jumlah creator aktif, proyek
 * unggulan terbaru, dan creator terbaru. Bila belum ada data sama sekali,
 * bagian tersebut tampil sebagai ajakan (bukan angka/placeholder palsu).
 *
 * @var int $creatorCount
 * @var array<string, mixed>|null $featuredProject
 * @var array<int, array<string, mixed>> $creatorFaces
 */
$featuredCover = $featuredProject['cover'] ?? null;
$featuredCoverUrl = $featuredCover !== null
    ? appUrl('/assets/uploads/' . rawurlencode((string) $featuredCover['stored_name']))
    : null;
$featuredLink = $featuredProject !== null
    ? appUrl('/project/' . (int) $featuredProject['id'])
    : appUrl('/explore/');
?>
<section class="hero" id="top">
    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow eyebrow-outline">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3.5 13.9 9 19.5 11 13.9 13 12 18.5 10.1 13 4.5 11 10.1 9Z"/>
                </svg>
                <?= escape(SITE_TAGLINE) ?>
            </p>
            <h1>Great work<br>deserves to be <em>seen.</em></h1>
            <p class="hero-description">
                Build a portfolio that feels like you. Share your craft, find inspiration,
                and connect with the world’s most ambitious creators.
            </p>
            <div class="hero-buttons">
                <a class="button" href="<?= escape(appUrl('/login/register/')) ?>" data-auth-open="register">
                    Start building — it’s free
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m13 6 6 6-6 6"/>
                    </svg>
                </a>
                <a class="button button-secondary" href="#discover">Explore portfolios</a>
            </div>

            <?php if ($creatorCount > 0): ?>
                <div class="community-proof" aria-label="<?= $creatorCount ?> creators sharing their work">
                    <div class="avatars" aria-hidden="true">
                        <?php if ($creatorFaces !== []): ?>
                            <?php foreach ($creatorFaces as $face): ?>
                                <?php
                                // Nama depan pembuat avatar: dua huruf awal.
                                $faceInitials = '';
                                foreach (preg_split('/\s+/', trim((string) $face['display_name'])) as $wordPart) {
                                    if ($wordPart !== '') {
                                        $faceInitials .= mb_strtoupper(mb_substr($wordPart, 0, 1));
                                    }
                                    if (mb_strlen($faceInitials) >= 2) {
                                        break;
                                    }
                                }
                                ?>
                                <span><?= escape($faceInitials !== '' ? $faceInitials : '?') ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span>PH</span>
                        <?php endif; ?>
                    </div>
                    <p>
                        <strong><?= escape(number_format($creatorCount)) ?>+</strong>
                        creator<?= $creatorCount === 1 ? '' : 's' ?> sharing their best work
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <div class="hero-gallery" aria-label="Featured portfolio work">
            <div class="gallery-card gallery-card-back">
                <img src="<?= escape(appUrl('/assets/images/geometry-dark.svg')) ?>" alt="" aria-hidden="true">
            </div>
            <div class="gallery-card gallery-card-bottom">
                <img src="<?= escape(appUrl('/assets/images/lines.svg')) ?>" alt="" aria-hidden="true">
                <span class="availability">
                    <b></b>
                    Available for work
                </span>
            </div>
            <figure class="gallery-card gallery-card-main">
                <?php if ($featuredCoverUrl !== null): ?>
                    <img src="<?= escape($featuredCoverUrl) ?>" alt="<?= escape((string) $featuredProject['title']) ?> oleh <?= escape((string) $featuredProject['display_name']) ?>" loading="lazy">
                <?php else: ?>
                    <img src="<?= escape(appUrl('/assets/images/architecture-blue.svg')) ?>" alt="" aria-hidden="true">
                <?php endif; ?>
                <figcaption>
                    <?php if ($featuredProject !== null): ?>
                        <small>Featured · <?= escape((string) $featuredProject['category']) ?></small>
                        <strong><?= escape((string) $featuredProject['title']) ?></strong>
                    <?php else: ?>
                        <small>Showcase · your work here</small>
                        <strong>Become the first</strong>
                    <?php endif; ?>
                </figcaption>
                <a class="gallery-card-link" href="<?= escape($featuredLink) ?>" aria-label="<?= $featuredProject !== null ? 'View ' . escape((string) $featuredProject['title']) : 'Explore work' ?>"></a>
            </figure>
        </div>
    </div>
</section>
