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
        <a href="<?= escape(appUrl('/studio/media.php')) ?>">Profile media</a>
        <a href="<?= escape(appUrl('/studio/logout.php')) ?>">Security / sign out</a>
    </nav>

    <form method="post" class="panel studio-form settings-form" id="public-profile">
        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

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

<?php require __DIR__ . '/layout-end.php'; ?>
