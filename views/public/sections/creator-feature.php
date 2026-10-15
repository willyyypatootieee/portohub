<?php
/**
 * Bagian "Meet the creator".
 *
 * Menampilkan creator unggulan (paling banyak karya published) beserta
 * sampul karyanya, diambil dari data nyata. Bila belum ada creator dengan
 * karya published, bagian ini menjadi ajakan untuk mulai berkarya.
 *
 * @var array<string, mixed>|null $featuredCreator
 */
$creatorCover = $featuredCreator['cover'] ?? null;
$creatorCoverUrl = $creatorCover !== null
    ? appUrl('/assets/uploads/' . rawurlencode((string) $creatorCover['stored_name']))
    : null;
$creatorProfileUrl = $featuredCreator !== null
    ? appUrl('/portfolio/' . rawurlencode((string) $featuredCreator['username']))
    : appUrl('/login/register/');
$creatorInitials = '';
if ($featuredCreator !== null) {
    foreach (preg_split('/\s+/', trim((string) $featuredCreator['display_name'])) as $wordPart) {
        if ($wordPart !== '') {
            $creatorInitials .= mb_strtoupper(mb_substr($wordPart, 0, 1));
        }
        if (mb_strlen($creatorInitials) >= 2) {
            break;
        }
    }
}
?>
<section class="creator-feature section" id="creator">
    <div class="container creator-panel">
        <div class="creator-image">
            <?php if ($creatorCoverUrl !== null): ?>
                <img src="<?= escape($creatorCoverUrl) ?>" alt="Karya <?= escape((string) $featuredCreator['display_name']) ?>" loading="lazy">
            <?php else: ?>
                <img src="<?= escape(appUrl('/assets/images/creator-spiral.svg')) ?>" alt="A black sculptural spiral staircase">
            <?php endif; ?>
            <span><?= $featuredCreator !== null ? 'Kreator unggulan' : 'Kreator pertama' ?></span>
        </div>
        <div class="creator-copy">
            <p class="eyebrow">Meet the creator</p>

            <?php if ($featuredCreator !== null): ?>
                <?php
                // Judul singkat dari headline creator, dipakai sebagai tajuk besar.
                $creatorHeadline = trim((string) $featuredCreator['headline']);
                ?>
                <h2><?= escape($creatorHeadline !== '' ? $creatorHeadline : 'Craft over noise.') ?></h2>

                <?php if (trim((string) $featuredCreator['bio']) !== ''): ?>
                    <blockquote><?= escape(mb_strimwidth(trim((string) $featuredCreator['bio']), 0, 200, '…')) ?></blockquote>
                <?php endif; ?>

                <div class="creator-person">
                    <span><?= escape($creatorInitials !== '' ? $creatorInitials : '?') ?></span>
                    <p>
                        <strong><?= escape((string) $featuredCreator['display_name']) ?></strong>
                        <small>@<?= escape((string) $featuredCreator['username']) ?> · <?= (int) $featuredCreator['project_count'] ?> karya published</small>
                    </p>
                </div>

                <a class="button button-dark-outline" href="<?= escape($creatorProfileUrl) ?>">
                    View <?= escape(explode(' ', trim((string) $featuredCreator['display_name']))[0]) ?>’s portfolio
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m13 6 6 6-6 6"/>
                    </svg>
                </a>
            <?php else: ?>
                <h2>Your work,<br>front and center.</h2>
                <blockquote>“Every portfolio here started with a single published project. Yours could be the next one we feature.”</blockquote>
                <div class="creator-person">
                    <span>PH</span>
                    <p>
                        <strong>Be the first featured creator</strong>
                        <small>Publish a project to be showcased here</small>
                    </p>
                </div>
                <a class="button button-dark-outline" href="<?= escape(appUrl('/login/register/')) ?>" data-auth-open="register">
                    Start building
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m13 6 6 6-6 6"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
