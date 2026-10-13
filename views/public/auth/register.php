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
    <link rel="stylesheet" href="<?= escape(appUrl('/assets/css/auth/register.css')) ?>">
</head>
<body>
    <main>
        <p><a href="<?= escape(appUrl('/')) ?>"><?= escape(SITE_NAME) ?></a></p>
        <h1>Buat portofolio yang mudah diingat</h1>
        <p>Mulai dengan membuat akun creator Anda.</p>

        <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">

            <?php
            $fields = [
                'name' => ['label' => 'Nama', 'type' => 'text', 'autocomplete' => 'name'],
                'email' => ['label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'],
            ];
            ?>
            <?php foreach ($fields as $name => $field): ?>
                <p>
                    <label>
                        <?= escape($field['label']) ?><br>
                        <input type="<?= escape($field['type']) ?>" name="<?= escape($name) ?>" value="<?= escape($values[$name]) ?>" autocomplete="<?= escape($field['autocomplete']) ?>" required>
                    </label>
                    <?php if (isset($errors[$name])): ?><small><?= escape($errors[$name]) ?></small><?php endif; ?>
                </p>
            <?php endforeach; ?>

            <p>
                <label>Kata sandi (minimal 8 karakter)<br><input type="password" name="password" autocomplete="new-password" required></label>
                <?php if (isset($errors['password'])): ?><small><?= escape($errors['password']) ?></small><?php endif; ?>
            </p>
            <p>
                <label>Konfirmasi kata sandi<br><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                <?php if (isset($errors['password_confirmation'])): ?><small><?= escape($errors['password_confirmation']) ?></small><?php endif; ?>
            </p>

            <button type="submit">Buat akun</button>
        </form>

        <p>Sudah punya akun? <a href="<?= escape(appUrl('/login/')) ?>">Masuk</a></p>
    </main>
</body>
</html>
