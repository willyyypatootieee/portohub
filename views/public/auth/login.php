<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($pageTitle) ?> — <?= escape(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <!-- Halaman auth memang tanpa gaya; modul CSS disiapkan untuk kebutuhan berikutnya. -->
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/auth/login.css')) ?>">
</head>
<body>
    <main>
        <p><a href="<?= escape(appUrl('/')) ?>"><?= escape(SITE_NAME) ?></a></p>
        <h1>Selamat datang kembali</h1>
        <p>Masuk untuk mengelola portofolio Anda.</p>

        <?php if (isset($errors['login'])): ?>
            <p role="alert"><?= escape($errors['login']) ?></p>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

            <p>
                <label>
                    Email<br>
                    <input type="email" name="email" value="<?= escape($values['email']) ?>" autocomplete="email" required>
                </label>
                <?php if (isset($errors['email'])): ?><small><?= escape($errors['email']) ?></small><?php endif; ?>
            </p>

            <p>
                <label>
                    Kata sandi<br>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <?php if (isset($errors['password'])): ?><small><?= escape($errors['password']) ?></small><?php endif; ?>
            </p>

            <button type="submit">Masuk</button>
        </form>

        <p>Belum punya akun? <a href="<?= escape(appUrl('/login/register/')) ?>">Daftar sekarang</a></p>
    </main>
</body>
</html>
