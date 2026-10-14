<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($pageTitle) ?> — <?= escape(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <!-- register.css memuat auth/auth.css lalu app.css (token, reset, tombol, alert). -->
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/auth/register.css')) ?>">
</head>
<body class="auth-page">
    <main class="auth-card">
        <a class="auth-brand" href="<?= escape(appUrl('/')) ?>"><?= escape(SITE_NAME) ?></a>
        <h1>Buat portofolio yang mudah diingat</h1>
        <p class="auth-intro">Mulai dengan membuat akun creator Anda.</p>

        <form class="auth-form" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

            <?php
            $fields = [
                'name' => ['label' => 'Nama', 'type' => 'text', 'autocomplete' => 'name'],
                'email' => ['label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'],
            ];
            ?>
            <?php foreach ($fields as $name => $field): ?>
                <label class="auth-field">
                    <span><?= escape($field['label']) ?></span>
                    <input type="<?= escape($field['type']) ?>" name="<?= escape($name) ?>" value="<?= escape($values[$name]) ?>" autocomplete="<?= escape($field['autocomplete']) ?>" required>
                    <?php if (isset($errors[$name])): ?><small class="auth-error"><?= escape($errors[$name]) ?></small><?php endif; ?>
                </label>
            <?php endforeach; ?>

            <label class="auth-field">
                <span>Kata sandi (minimal 8 karakter)</span>
                <input type="password" name="password" autocomplete="new-password" required>
                <?php if (isset($errors['password'])): ?><small class="auth-error"><?= escape($errors['password']) ?></small><?php endif; ?>
            </label>

            <label class="auth-field">
                <span>Konfirmasi kata sandi</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
                <?php if (isset($errors['password_confirmation'])): ?><small class="auth-error"><?= escape($errors['password_confirmation']) ?></small><?php endif; ?>
            </label>

            <button class="button" type="submit">Buat akun</button>
        </form>

        <p class="auth-footer">Sudah punya akun? <a href="<?= escape(appUrl('/login/')) ?>">Masuk</a></p>
    </main>
</body>
</html>
