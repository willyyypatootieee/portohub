<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Superadmin sign in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/studio.css')) ?>">
</head>
<body class="superadmin-login">
    <main class="superadmin-card">
        <a class="studio-brand" href="<?= escape(appUrl('/')) ?>">
            <span>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5"/><path d="M6 9.5V19a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9.5"/></svg>
            </span>
            <?= escape(SITE_NAME) ?>
        </a>
        <p class="eyebrow">Restricted monitoring</p>
        <h1>Superadmin sign in</h1>

        <?php if ($loginError): ?>
            <p class="studio-alert error"><?= escape($loginError) ?></p>
        <?php endif; ?>

        <form method="post" class="studio-form">
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

            <label>
                Username
                <input name="username" autocomplete="username" required>
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <button class="button" type="submit">Sign in</button>
        </form>
    </main>
</body>
</html>
