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
            <div class="community-proof" aria-label="More than 50,000 creators sharing their work">
                <div class="avatars" aria-hidden="true">
                    <span>MC</span>
                    <span>JB</span>
                    <span>AW</span>
                    <span>TP</span>
                </div>
                <p><strong>50,000+</strong> creators sharing their best work</p>
            </div>
        </div>
        <div class="hero-gallery" aria-label="Featured portfolio work">
            <div class="gallery-card gallery-card-back">
                <img src="<?= escape(appUrl('/assets/images/geometry-dark.svg')) ?>" alt="Abstract dark blue architecture">
            </div>
            <div class="gallery-card gallery-card-bottom">
                <img src="<?= escape(appUrl('/assets/images/lines.svg')) ?>" alt="Curving monochrome architectural lines">
                <span class="availability">
                    <b></b>
                    Available for work
                </span>
            </div>
            <figure class="gallery-card gallery-card-main">
                <img src="<?= escape(appUrl('/assets/images/architecture-blue.svg')) ?>" alt="Blue modern building viewed from below">
                <figcaption>
                    <small>FEATURED · PHOTOGRAPHY</small>
                    <strong>Sculpting silence</strong>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
