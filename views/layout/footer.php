    </main>
    <footer class="site-footer" id="footer">
        <div class="container footer-inner">
            <a class="brand" href="<?= escape(appUrl('/')) ?>" aria-label="<?= escape(SITE_NAME) ?> home">
                <span class="brand-mark" aria-hidden="true">
                    <i></i>
                    <i></i>
                    <i></i>
                </span>
                <span><?= SITE_NAME ?></span>
            </a>
            <p>© <?= SITE_YEAR ?> <?= SITE_NAME ?>. Made for love in TRM PS 09.07.</p>
            <nav aria-label="Footer navigation">
                <a href="#discover">Instagram</a>
                <a href="#creator">LinkedIn</a>
                <a href="#footer">Privacy</a>
            </nav>
        </div>
    </footer>
    <?php foreach (($pageScripts ?? []) as $script): ?>
        <script src="<?= escape(appUrl($script)) ?>"></script>
    <?php endforeach; ?>
    <script src="<?= escape(appUrl('/assets/js/app.js')) ?>"></script>
</body>
</html>
