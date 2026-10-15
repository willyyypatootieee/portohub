<?php
/**
 * Superadmin — daftar seluruh proyek dan portofolio.
 *
 * @var array<int, array<string, mixed>> $projects
 * @var string $search
 * @var string $status
 * @var string $visibility
 * @var int $totalProjects
 * @var int $currentPage
 * @var int $totalPages
 * @var callable(): string $listQuery
 */
$page = 'projects';
$pageTitle = 'Content';
require __DIR__ . '/layout.php';

$baseQuery = $listQuery();
$pageHref = static fn (int $number): string => appUrl('/login/superadmin/projects.php')
    . $baseQuery . ($baseQuery === '' ? '?' : '&') . 'page=' . $number;
?>

<p class="eyebrow">Content moderation</p>
<h1>Projects &amp; portfolios</h1>
<p class="admin-description">
    Every project published across the platform. Hide anything that does not meet the platform guidelines —
    hidden work disappears from the landing page and public portfolios.
</p>

<form class="admin-toolbar" method="get" action="<?= escape(appUrl('/login/superadmin/projects.php')) ?>">
    <div class="admin-search">
        <span aria-hidden="true">⌕</span>
        <input type="search" name="q" value="<?= escape($search) ?>" placeholder="Search project, category or creator" aria-label="Search projects">
    </div>

    <label class="admin-select">
        <span>Status</span>
        <select name="status">
            <option value="all"<?= $status === 'all' ? ' selected' : '' ?>>All statuses</option>
            <option value="published"<?= $status === 'published' ? ' selected' : '' ?>>Published</option>
            <option value="draft"<?= $status === 'draft' ? ' selected' : '' ?>>Draft</option>
            <option value="archived"<?= $status === 'archived' ? ' selected' : '' ?>>Archived</option>
        </select>
    </label>

    <label class="admin-select">
        <span>Visibility</span>
        <select name="visibility">
            <option value="all"<?= $visibility === 'all' ? ' selected' : '' ?>>All</option>
            <option value="visible"<?= $visibility === 'visible' ? ' selected' : '' ?>>Visible</option>
            <option value="hidden"<?= $visibility === 'hidden' ? ' selected' : '' ?>>Hidden</option>
        </select>
    </label>

    <button class="button button-small" type="submit">Filter</button>
    <?php if ($search !== '' || $status !== 'all' || $visibility !== 'all'): ?>
        <a class="admin-reset" href="<?= escape(appUrl('/login/superadmin/projects.php')) ?>">Reset</a>
    <?php endif; ?>
</form>

<section class="panel">
    <div class="panel-title">
        <div>
            <h2><?= (int) $totalProjects ?> project<?= $totalProjects === 1 ? '' : 's' ?></h2>
            <p>Hidden projects stay in the creator's studio but are removed from every public page.</p>
        </div>
    </div>

    <?php if ($projects === []): ?>
        <div class="empty-state">
            <strong>No projects found</strong>
            <p>Try a different keyword or clear the filters.</p>
        </div>
    <?php else: ?>
        <div class="project-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Creator</th>
                        <th>Media</th>
                        <th>Status</th>
                        <th>Visibility</th>
                        <th>Updated</th>
                        <th class="is-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <?php $isHidden = (int) $project['is_hidden'] === 1; ?>
                        <tr class="<?= $isHidden ? 'is-hidden-row' : '' ?>">
                            <td>
                                <strong><?= escape($project['title']) ?></strong>
                                <small><?= escape($project['category']) ?></small>
                            </td>
                            <td>
                                <strong><?= escape($project['display_name']) ?></strong>
                                <small>@<?= escape($project['username']) ?></small>
                            </td>
                            <td><?= (int) $project['media_count'] ?> file<?= (int) $project['media_count'] === 1 ? '' : 's' ?></td>
                            <td>
                                <span class="status <?= escape($project['status']) ?>"><?= escape(ucfirst((string) $project['status'])) ?></span>
                            </td>
                            <td>
                                <?php if ($isHidden): ?>
                                    <span class="status hidden">Hidden</span>
                                <?php else: ?>
                                    <span class="status visible">Visible</span>
                                <?php endif; ?>
                            </td>
                            <td><?= escape(date('M j, Y', strtotime((string) $project['updated_at']))) ?></td>
                            <td class="is-actions">
                                <div class="row-actions">
                                    <a class="button-small button-outline" href="<?= escape(appUrl('/portfolio/?user=' . rawurlencode((string) $project['username']))) ?>" target="_blank" rel="noopener">Portfolio</a>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                                        <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                                        <input type="hidden" name="action" value="<?= $isHidden ? 'unhide' : 'hide' ?>">
                                        <button class="button-small <?= $isHidden ? 'button-secondary' : 'button-danger' ?>" type="submit">
                                            <?= $isHidden ? 'Unhide' : 'Hide' ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <nav class="admin-pagination" aria-label="Project pages">
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
