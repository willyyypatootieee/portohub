    </main>
    <footer class="site-footer" id="footer">
        <div class="container footer-top">
            <div class="footer-brand">
                <a class="brand" href="<?= escape(appUrl('/')) ?>" aria-label="<?= escape(SITE_NAME) ?> home">
                    <span class="brand-mark" aria-hidden="true">
                        <i></i>
                        <i></i>
                        <i></i>
                    </span>
                    <span><?= SITE_NAME ?></span>
                </a>
                <p>A curated home for outstanding creative portfolios. Show your work, get discovered.</p>
            </div>

            <nav class="footer-links" aria-label="Explore PortfolioHub">
                <p class="footer-heading">Discover</p>
                <a href="<?= escape(appUrl('/explore/')) ?>">Explore work</a>
                <a href="<?= escape(appUrl('/explore/?tab=creators')) ?>">Creators</a>
                <a href="<?= escape(appUrl('/login/register/')) ?>">Become a creator</a>
            </nav>

            <nav class="footer-links" aria-label="Creator tools">
                <p class="footer-heading">Creator</p>
                <a href="<?= escape(appUrl('/studio/')) ?>">Creator studio</a>
                <a href="<?= escape(appUrl('/login/')) ?>">Log in</a>
                <a href="<?= escape(appUrl('/login/register/')) ?>">Get started</a>
            </nav>

            <nav class="footer-links" aria-label="Follow PortfolioHub">
                <p class="footer-heading">Follow</p>
                <a href="#footer">Instagram</a>
                <a href="#footer">LinkedIn</a>
                <a href="#footer">Privacy</a>
            </nav>
        </div>

        <div class="container footer-bottom">
            <p>© <?= SITE_YEAR ?> <?= SITE_NAME ?>. Made for love in TRM PS 09.07.</p>
            <p>Free forever · No credit card required</p>
        </div>
    </footer>
    <?php foreach (($pageScripts ?? []) as $script): ?>
        <script src="<?= escape(appUrl($script)) ?>"></script>
    <?php endforeach; ?>
    <script src="<?= escape(appUrl('/assets/js/app.js')) ?>"></script>
</body>
</html>
