<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($pageTitle) ?> — <?= escape(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <!-- login.css memuat auth/auth.css lalu app.css (token, reset, tombol, alert). -->
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/auth/login.css')) ?>">
</head>
<body class="auth-page">
    <main class="auth-card">
        <a class="auth-brand" href="<?= escape(appUrl('/')) ?>"><?= escape(SITE_NAME) ?></a>
        <h1>Selamat datang kembali</h1>
        <p class="auth-intro">Masuk untuk mengelola portofolio Anda.</p>

        <?php if (isset($errors['login'])): ?>
            <p class="studio-alert error" role="alert"><?= escape($errors['login']) ?></p>
        <?php endif; ?>

        <form class="auth-form" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

            <label class="auth-field">
                <span>Email</span>
                <input type="email" name="email" value="<?= escape($values['email']) ?>" autocomplete="email" required>
                <?php if (isset($errors['email'])): ?><small class="auth-error"><?= escape($errors['email']) ?></small><?php endif; ?>
            </label>

            <label class="auth-field">
                <span>Kata sandi</span>
                <input type="password" name="password" autocomplete="current-password" required>
                <?php if (isset($errors['password'])): ?><small class="auth-error"><?= escape($errors['password']) ?></small><?php endif; ?>
            </label>

            <button class="button" type="submit">Masuk</button>
        </form>

        <p class="auth-footer">Belum punya akun? <a href="<?= escape(appUrl('/login/register/')) ?>">Daftar sekarang</a></p>
    </main>
</body>
</html>


