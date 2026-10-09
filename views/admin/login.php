<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin — <?= escape(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-login-page">
    <main class="login-card">
        <a class="admin-brand" href="../index.php">PortfolioHub</a>
        <p class="admin-kicker">Area pengelolaan</p>
        <h1>Masuk ke dashboard</h1>
        <p class="login-description">Kelola karya yang tampil pada halaman utama dari satu tempat.</p>

        <?php if (!empty($loginError)): ?>
            <p class="alert alert-error" role="alert"><?= escape($loginError) ?></p>
        <?php endif; ?>

        <form method="post" class="admin-form">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
            <label>
                Nama pengguna
                <input type="text" name="username" autocomplete="username" required autofocus>
            </label>
            <label>
                Kata sandi
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit" class="admin-button">Masuk</button>
        </form>
        <a class="back-link" href="../index.php">← Kembali ke landing page</a>
    </main>
</body>
</html>
