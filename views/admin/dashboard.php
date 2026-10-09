<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin — <?= escape(SITE_NAME) ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-page">
    <header class="admin-header">
        <div class="admin-container admin-header-inner">
            <a class="admin-brand" href="../index.php">PortfolioHub</a>
            <nav>
                <a href="../index.php" target="_blank" rel="noopener">Lihat situs</a>
                <a href="index.php?logout=1">Keluar</a>
            </nav>
        </div>
    </header>

    <main class="admin-container admin-main">
        <div class="admin-title-row">
            <div>
                <p class="admin-kicker">Dashboard</p>
                <h1>Kelola karya portofolio</h1>
                <p>Tambah, ubah, atau hapus kartu karya yang tampil di landing page.</p>
            </div>
            <a class="admin-button" href="index.php#form-karya">Tambah karya</a>
        </div>

        <?php if ($flashMessage): ?>
            <p class="alert alert-success" role="status"><?= escape($flashMessage) ?></p>
        <?php endif; ?>

        <section class="admin-panel" id="form-karya">
            <div class="panel-heading">
                <h2><?= $editingItem ? 'Ubah karya' : 'Tambah karya baru' ?></h2>
                <?php if ($editingItem): ?>
                    <a href="index.php">Batal ubah</a>
                <?php endif; ?>
            </div>
            <form method="post" class="admin-form item-form">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int) ($editingItem['id'] ?? 0) ?>">
                <label>
                    Judul karya
                    <input type="text" name="title" value="<?= escape($editingItem['title'] ?? '') ?>" required>
                </label>
                <label>
                    Nama kreator
                    <input type="text" name="creator_name" value="<?= escape($editingItem['creator_name'] ?? '') ?>" required>
                </label>
                <label>
                    Kategori
                    <input type="text" name="category" value="<?= escape($editingItem['category'] ?? '') ?>" placeholder="Contoh: Photography" required>
                </label>
                <label>
                    Nama file gambar
                    <input type="text" name="image_filename" value="<?= escape($editingItem['image_filename'] ?? '') ?>" placeholder="Contoh: architecture-blue.svg" required>
                    <small>Simpan gambar di folder <code>assets/images/</code>. Masukkan nama filenya saja.</small>
                </label>
                <label>
                    Jumlah suka
                    <input type="number" name="likes_count" min="0" value="<?= (int) ($editingItem['likes_count'] ?? 0) ?>" required>
                </label>
                <label>
                    Jumlah dilihat
                    <input type="number" name="views_count" min="0" value="<?= (int) ($editingItem['views_count'] ?? 0) ?>" required>
                </label>
                <label>
                    Urutan tampil
                    <input type="number" name="display_order" value="<?= (int) ($editingItem['display_order'] ?? 0) ?>" required>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="is_featured" value="1" <?= !empty($editingItem['is_featured']) ? 'checked' : '' ?>>
                    Jadikan karya unggulan
                </label>
                <div class="form-actions">
                    <button type="submit" class="admin-button">
                        <?= $editingItem ? 'Simpan perubahan' : 'Simpan karya' ?>
                    </button>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <div class="panel-heading">
                <h2>Daftar karya</h2>
                <span><?= count($portfolioItems) ?> karya</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Karya</th>
                            <th>Kategori</th>
                            <th>Gambar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portfolioItems as $item): ?>
                            <tr>
                                <td><?= (int) $item['display_order'] ?></td>
                                <td><strong><?= escape($item['title']) ?></strong><small><?= escape($item['creator_name']) ?></small></td>
                                <td><?= escape($item['category']) ?></td>
                                <td><code><?= escape($item['image_filename']) ?></code></td>
                                <td><?= (int) $item['is_featured'] === 1 ? 'Unggulan' : 'Reguler' ?></td>
                                <td class="table-actions">
                                    <a href="index.php?edit=<?= (int) $item['id'] ?>#form-karya">Ubah</a>
                                    <form method="post" onsubmit="return confirm('Hapus karya ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <button type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
