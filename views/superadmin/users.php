<?php
/**
 * Superadmin — daftar dan pencarian pengguna.
 *
 * @var array<int, array<string, mixed>> $users
 * @var string $search
 * @var string $status
 * @var int $totalUsers
 * @var int $currentPage
 * @var int $totalPages
 * @var int $perPage
 * @var callable(): string $listQuery
 */
$page = 'users';
$pageTitle = 'Users';
require __DIR__ . '/layout.php';

$baseQuery = $listQuery();
$pageHref = static fn (int $number): string => appUrl('/login/superadmin/users.php')
    . $baseQuery . ($baseQuery === '' ? '?' : '&') . 'page=' . $number;
?>

<p class="eyebrow">User directory</p>
<h1>Users</h1>
<p class="admin-description">
    Search every registered creator and control who can sign in to the platform.
</p>

<form class="admin-toolbar" method="get" action="<?= escape(appUrl('/login/superadmin/users.php')) ?>">
    <div class="admin-search">
        <span aria-hidden="true">⌕</span>
        <input type="search" name="q" value="<?= escape($search) ?>" placeholder="Search name, username or email" aria-label="Search users">
    </div>

    <label class="admin-select">
        <span>Status</span>
        <select name="status">
            <option value="all"<?= $status === 'all' ? ' selected' : '' ?>>All accounts</option>
            <option value="active"<?= $status === 'active' ? ' selected' : '' ?>>Active only</option>
            <option value="inactive"<?= $status === 'inactive' ? ' selected' : '' ?>>Deactivated only</option>
        </select>
    </label>

    <button class="button button-small" type="submit">Filter</button>
    <?php if ($search !== '' || $status !== 'all'): ?>
        <a class="admin-reset" href="<?= escape(appUrl('/login/superadmin/users.php')) ?>">Reset</a>
    <?php endif; ?>
</form>

<section class="panel">
    <div class="panel-title">
        <div>
            <h2><?= (int) $totalUsers ?> account<?= $totalUsers === 1 ? '' : 's' ?></h2>
            <p>Deactivated creators keep their data but cannot sign in or use the studio.</p>
        </div>
    </div>

    <?php if ($users === []): ?>
        <div class="empty-state">
            <strong>No users found</strong>
            <p>Try a different keyword or clear the filters.</p>
        </div>
    <?php else: ?>
        <div class="project-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Creator</th>
                        <th>Email</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="is-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $account): ?>
                        <?php $isActive = (int) $account['is_active'] === 1; ?>
                        <tr>
                            <td>
                                <strong><?= escape($account['display_name']) ?></strong>
                                <small>@<?= escape($account['username']) ?></small>
                            </td>
                            <td><?= escape($account['email']) ?></td>
                            <td>
                                <?= (int) $account['published_count'] ?> published
                                <small><?= (int) $account['project_count'] ?> total</small>
                            </td>
                            <td>
                                <?php if ($isActive): ?>
                                    <span class="status published">Active</span>
                                <?php else: ?>
                                    <span class="status archived">Deactivated</span>
                                <?php endif; ?>
                            </td>
                            <td><?= escape(date('M j, Y', strtotime((string) $account['created_at']))) ?></td>
                            <td class="is-actions">
                                <div class="row-actions">
                                    <button class="button-small button-secondary" type="button" data-admin-edit="<?= (int) $account['id'] ?>">
                                        Edit
                                    </button>
                                    <a class="button-small button-outline" href="<?= escape(appUrl('/portfolio/?user=' . rawurlencode((string) $account['username']))) ?>" target="_blank" rel="noopener">Portfolio</a>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                        <input type="hidden" name="user_id" value="<?= (int) $account['id'] ?>">
                                        <input type="hidden" name="action" value="<?= $isActive ? 'deactivate' : 'activate' ?>">
                                        <button class="button-small <?= $isActive ? 'button-danger' : 'button-secondary' ?>" type="submit">
                                            <?= $isActive ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Delete <?= escape($account['display_name']) ?> and all of their projects and media? This cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                        <input type="hidden" name="user_id" value="<?= (int) $account['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="button-small button-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="admin-edit-row" data-admin-edit-row="<?= (int) $account['id'] ?>" hidden>
                            <td colspan="6">
                                <form method="post" class="admin-edit-form">
                                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                    <input type="hidden" name="user_id" value="<?= (int) $account['id'] ?>">
                                    <input type="hidden" name="action" value="update">

                                    <label>
                                        Display name
                                        <input name="display_name" value="<?= escape($account['display_name']) ?>" required>
                                    </label>
                                    <label>
                                        Username
                                        <input name="username" value="<?= escape($account['username']) ?>" required>
                                    </label>
                                    <label>
                                        Email
                                        <input type="email" name="email" value="<?= escape($account['email']) ?>" required>
                                    </label>
                                    <label>
                                        Headline
                                        <input name="headline" value="<?= escape($account['headline']) ?>" required>
                                    </label>

                                    <div class="admin-edit-actions">
                                        <button class="button-small" type="submit">Save changes</button>
                                        <button class="button-small button-secondary" type="button" data-admin-edit-close="<?= (int) $account['id'] ?>">Cancel</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="admin-pagination" aria-label="User pages">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= escape($pageHref($currentPage - 1)) ?>">← Prev</a>
                <?php endif; ?>
                <span>Page <?= (int) $currentPage ?> of <?= (int) $totalPages ?></span>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= escape($pageHref($currentPage + 1)) ?>">Next →</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/layout-end.php'; ?>
