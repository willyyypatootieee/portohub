<?php
/** Dashboard superadmin: ringkasan angka platform dan aktivitas creator terbaru. */
$page = 'overview';
$pageTitle = 'Superadmin overview';
require __DIR__ . '/layout.php';
?>

<p class="eyebrow">Platform monitoring</p>
<h1>Superadmin overview</h1>
<p class="admin-description">
    Monitor creators and moderate published content. This screen is separate from the creator studio.
</p>

<section class="metric-grid">
    <article>
        <p>Registered creators</p>
        <strong><?= (int) $counts['creators'] ?></strong>
    </article>
    <article>
        <p>Total projects</p>
        <strong><?= (int) $counts['projects'] ?></strong>
    </article>
    <article>
        <p>Published projects</p>
        <strong><?= (int) $counts['published'] ?></strong>
    </article>
    <article>
        <p>Hidden projects</p>
        <strong><?= (int) $counts['hidden'] ?></strong>
    </article>
    <article>
        <p>Deactivated accounts</p>
        <strong><?= (int) $counts['inactive'] ?></strong>
    </article>
    <article>
        <p>Uploaded assets</p>
        <strong><?= (int) $counts['assets'] ?></strong>
    </article>
</section>

<div class="admin-split">
    <section class="panel">
        <div class="panel-title">
            <div>
                <h2>Creator activity</h2>
                <p>Recent creator records and project activity.</p>
            </div>
            <a href="<?= escape(appUrl('/login/superadmin/users.php')) ?>">Manage users</a>
        </div>

        <?php if ($activity === []): ?>
            <div class="empty-state">
                <strong>Become The One</strong>
                <p>No registered creators yet.</p>
            </div>
        <?php else: ?>
            <div class="project-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Creator</th>
                            <th>Projects</th>
                            <th>Status</th>
                            <th>Last activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activity as $creator): ?>
                            <tr>
                                <td>
                                    <strong><?= escape($creator['display_name']) ?></strong>
                                    <small>@<?= escape($creator['username']) ?></small>
                                </td>
                                <td><?= (int) $creator['project_count'] ?></td>
                                <td>
                                    <?php if ((int) $creator['is_active'] === 1): ?>
                                        <span class="status published">Active</span>
                                    <?php else: ?>
                                        <span class="status archived">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= escape($creator['last_activity'] ?? 'No projects yet') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <aside class="panel admin-quick">
        <div class="panel-title">
            <div>
                <h2>Quick actions</h2>
                <p>Jump straight to moderation tasks.</p>
            </div>
        </div>
        <a class="admin-quick-link" href="<?= escape(appUrl('/login/superadmin/users.php')) ?>">
            <strong>User directory</strong>
            <span>Search accounts and enable or disable access.</span>
        </a>
        <a class="admin-quick-link" href="<?= escape(appUrl('/login/superadmin/projects.php')) ?>">
            <strong>Content moderation</strong>
            <span>Review every project and hide what breaks the rules.</span>
        </a>
        <a class="admin-quick-link" href="<?= escape(appUrl('/login/superadmin/projects.php?visibility=hidden')) ?>">
            <strong>Hidden projects</strong>
            <span>See everything currently removed from public view.</span>
        </a>
    </aside>
</div>

<?php require __DIR__ . '/layout-end.php'; ?>
