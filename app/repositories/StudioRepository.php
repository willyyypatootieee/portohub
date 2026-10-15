<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class StudioRepository
{
    public function __construct(private PDO $database)
    {
    }

    /**
     * Memeriksa tabel creator tanpa melakukan perubahan struktur database.
     * Struktur tabel dikelola lewat file SQL di folder database/.
     */
    public function hasRequiredSchema(): bool
    {
        $requiredTables = [
            'users',
            'studio_projects',
            'media_assets',
        ];

        $placeholders = implode(', ', array_fill(0, count($requiredTables), '?'));
        $statement = $this->database->prepare(
            'SELECT COUNT(*)
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name IN (' . $placeholders . ')'
        );
        $statement->execute($requiredTables);

        return (int) $statement->fetchColumn() === count($requiredTables);
    }

    /** @return array<string, mixed>|null */
    public function findUserByEmail(string $email): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        return $user === false ? null : $user;
    }

    /** @return array<string, mixed>|null */
    public function findUser(int $userId): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $userId]);
        $user = $statement->fetch();
        return $user === false ? null : $user;
    }

    /** @return array<string, mixed>|null */
    public function findUserByUsername(string $username): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public function emailExists(string $email, ?int $exceptUserId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = ['email' => $email];
        if ($exceptUserId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptUserId;
        }
        $statement = $this->database->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public function usernameExists(string $username, ?int $exceptUserId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = :username';
        $params = ['username' => $username];
        if ($exceptUserId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptUserId;
        }
        $statement = $this->database->prepare($sql);
        $statement->execute($params);
        return (int) $statement->fetchColumn() > 0;
    }

    public function createUser(string $name, string $email, string $password): int
    {
        $username = $this->makeAvailableUsername($name);
        $statement = $this->database->prepare('INSERT INTO users (name, email, password_hash, display_name, username, headline, bio) VALUES (:name, :email, :password_hash, :display_name, :username, :headline, :bio)');
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'display_name' => $name,
            'username' => $username,
            'headline' => 'Creative professional',
            'bio' => 'Tell the world about your work.',
        ]);
        return (int) $this->database->lastInsertId();
    }

    /** @param array<string, string> $profile */
    public function updateProfile(int $userId, array $profile): void
    {
        $statement = $this->database->prepare(
            'UPDATE users
             SET name = :name,
                 display_name = :display_name,
                 username = :username,
                 headline = :headline,
                 bio = :bio
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $userId,
            'name' => $profile['display_name'],
            'display_name' => $profile['display_name'],
            'username' => $profile['username'],
            'headline' => $profile['headline'],
            'bio' => $profile['bio'],
        ]);
    }

    public function updateCv(int $userId, string $storedName, string $originalName): void
    {
        $statement = $this->database->prepare(
            'UPDATE users
             SET cv_filename = :cv_filename,
                 cv_original_name = :cv_original_name,
                 cv_uploaded_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $userId,
            'cv_filename' => $storedName,
            'cv_original_name' => $originalName,
        ]);
    }

    public function clearCv(int $userId): void
    {
        $statement = $this->database->prepare(
            'UPDATE users
             SET cv_filename = NULL, cv_original_name = NULL, cv_uploaded_at = NULL
             WHERE id = :id'
        );
        $statement->execute(['id' => $userId]);
    }

    /**
     * Proyek milik satu creator.
     *
     * Studio milik creator sendiri memakai $includeHidden = true agar proyek
     * yang disembunyikan pengelola tetap terlihat (dengan penanda khusus).
     * Halaman publik memakai $includeHidden = false supaya proyek yang
     * disembunyikan tidak pernah ikut tampil.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProjects(int $userId, ?string $status = null, string $search = '', bool $includeHidden = true): array
    {
        $sql = 'SELECT p.*, (SELECT COUNT(*) FROM media_assets m WHERE m.project_id = p.id) AS media_count FROM studio_projects p WHERE p.user_id = :user_id';
        $params = ['user_id' => $userId];
        if (!$includeHidden) {
            $sql .= ' AND p.is_hidden = 0';
        }
        if ($status !== null && in_array($status, ['draft', 'published', 'archived'], true)) {
            $sql .= ' AND p.status = :status';
            $params['status'] = $status;
        }
        if ($search !== '') {
            $sql .= ' AND (p.title LIKE :search OR p.category LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY p.sort_order ASC, p.updated_at DESC, p.id DESC';
        $statement = $this->database->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    /**
     * Memindahkan satu proyek naik atau turun satu posisi.
     *
     * Urutan ditulis ulang menjadi 1..n supaya posisi tetap konsisten walau
     * sebelumnya ada proyek yang masih memakai nilai default 0.
     */
    public function reorderProject(int $userId, int $projectId, string $direction): bool
    {
        if (!in_array($direction, ['up', 'down'], true)) {
            return false;
        }

        $statement = $this->database->prepare(
            'SELECT id FROM studio_projects WHERE user_id = :user_id ORDER BY sort_order ASC, updated_at DESC, id DESC'
        );
        $statement->execute(['user_id' => $userId]);
        $ids = array_map('intval', array_column($statement->fetchAll(), 'id'));

        $index = array_search($projectId, $ids, true);
        if ($index === false) {
            return false;
        }

        $target = $direction === 'up' ? $index - 1 : $index + 1;
        if ($target < 0 || $target >= count($ids)) {
            return false;
        }

        [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];

        $update = $this->database->prepare(
            'UPDATE studio_projects SET sort_order = :sort_order WHERE id = :id AND user_id = :user_id'
        );

        foreach ($ids as $position => $id) {
            $update->execute([
                'sort_order' => $position + 1,
                'id' => $id,
                'user_id' => $userId,
            ]);
        }

        return true;
    }

    /** @return array<string, mixed>|null */
    public function findProject(int $userId, int $projectId): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM studio_projects WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $projectId, 'user_id' => $userId]);
        $project = $statement->fetch();
        return $project === false ? null : $project;
    }

    /** @param array<string, string|int|null> $project */
    public function saveProject(int $userId, array $project, ?int $projectId = null): int
    {
        if ($projectId !== null) {
            $project['id'] = $projectId;
            $project['user_id'] = $userId;
            $statement = $this->database->prepare(
                'UPDATE studio_projects
                 SET title = :title,
                     category = :category,
                     description = :description,
                     technologies = :technologies,
                     demo_url = :demo_url,
                     repo_url = :repo_url,
                     status = :status,
                     featured = :featured
                 WHERE id = :id AND user_id = :user_id'
            );
            $statement->execute($project);
            return $projectId;
        }

        $project['user_id'] = $userId;
        $statement = $this->database->prepare(
            'INSERT INTO studio_projects (user_id, title, category, description, technologies, demo_url, repo_url, status, featured)
             VALUES (:user_id, :title, :category, :description, :technologies, :demo_url, :repo_url, :status, :featured)'
        );
        $statement->execute($project);
        return (int) $this->database->lastInsertId();
    }

    public function deleteProject(int $userId, int $projectId): void
    {
        $statement = $this->database->prepare('DELETE FROM studio_projects WHERE id = :id AND user_id = :user_id');
        $statement->execute(['id' => $projectId, 'user_id' => $userId]);
    }

    /** @return array<int, array<string, mixed>> */
    public function getMedia(int $userId, string $type = 'all'): array
    {
        $sql = 'SELECT m.*, p.title AS project_title FROM media_assets m LEFT JOIN studio_projects p ON p.id = m.project_id WHERE m.user_id = :user_id';
        $params = ['user_id' => $userId];
        $typeFilters = ['images' => 'image/%', 'video' => 'video/%', 'documents' => 'application/pdf'];
        if (isset($typeFilters[$type])) {
            $sql .= ' AND m.mime_type LIKE :mime_type';
            $params['mime_type'] = $typeFilters[$type];
        }
        $sql .= ' ORDER BY m.created_at DESC, m.id DESC';
        $statement = $this->database->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function addMedia(int $userId, ?int $projectId, string $originalName, string $storedName, string $mimeType, int $fileSize): void
    {
        $statement = $this->database->prepare('INSERT INTO media_assets (user_id, project_id, original_name, stored_name, mime_type, file_size) VALUES (:user_id, :project_id, :original_name, :stored_name, :mime_type, :file_size)');
        $statement->execute(['user_id' => $userId, 'project_id' => $projectId, 'original_name' => $originalName, 'stored_name' => $storedName, 'mime_type' => $mimeType, 'file_size' => $fileSize]);
    }

    /** @return array<string, mixed>|null */
    public function deleteMedia(int $userId, int $mediaId): ?array
    {
        $find = $this->database->prepare('SELECT * FROM media_assets WHERE id = :id AND user_id = :user_id');
        $find->execute(['id' => $mediaId, 'user_id' => $userId]);
        $media = $find->fetch();
        if ($media === false) {
            return null;
        }
        $delete = $this->database->prepare('DELETE FROM media_assets WHERE id = :id AND user_id = :user_id');
        $delete->execute(['id' => $mediaId, 'user_id' => $userId]);
        return $media;
    }

    /** @return array<int, array<string, mixed>> */
    public function getProjectMedia(int $userId, int $projectId): array
    {
        $statement = $this->database->prepare(
            'SELECT *
             FROM media_assets
             WHERE user_id = :user_id AND project_id = :project_id
             ORDER BY created_at DESC, id DESC'
        );
        $statement->execute(['user_id' => $userId, 'project_id' => $projectId]);
        return $statement->fetchAll();
    }

    /**
     * Karya yang tampil di landing page: proyek published milik semua creator.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getShowcaseProjects(int $limit = 18): array
    {
        $statement = $this->database->prepare(
            "SELECT p.id, p.title, p.category, p.description, p.technologies, p.demo_url, p.repo_url, p.updated_at,
                    u.display_name, u.username, u.headline,
                    (SELECT COUNT(*) FROM media_assets m WHERE m.project_id = p.id) AS media_count
             FROM studio_projects p
             INNER JOIN users u ON u.id = p.user_id
             WHERE p.status = 'published' AND p.is_hidden = 0
             ORDER BY p.featured DESC, p.sort_order ASC, p.updated_at DESC, p.id DESC
             LIMIT :limit"
        );
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        $projects = $statement->fetchAll();

        foreach ($projects as $index => $project) {
            $projects[$index]['cover'] = $this->findProjectCover((int) $project['id']);
        }

        return $projects;
    }

    /** @return array<int, string> */
    public function getShowcaseCategories(): array
    {
        $statement = $this->database->query(
            "SELECT DISTINCT category
             FROM studio_projects
             WHERE status = 'published' AND is_hidden = 0 AND category <> ''
             ORDER BY category ASC"
        );
        return array_column($statement->fetchAll(), 'category');
    }

    /**
     * Gambar sampul proyek.
     *
     * Sampul yang diunggah khusus lewat form proyek (kolom cover_filename)
     * selalu diprioritaskan. Bila belum ada, gambar pertama pada media proyek
     * dipakai sebagai sampul cadangan.
     *
     * @return array{stored_name: string, mime_type: string}|null
     */
    public function findProjectCover(int $projectId): ?array
    {
        $statement = $this->database->prepare(
            'SELECT cover_filename FROM studio_projects WHERE id = :project_id LIMIT 1'
        );
        $statement->execute(['project_id' => $projectId]);
        $coverFilename = $statement->fetchColumn();

        if (is_string($coverFilename) && $coverFilename !== '') {
            return ['stored_name' => $coverFilename, 'mime_type' => 'image/*'];
        }

        $statement = $this->database->prepare(
            "SELECT stored_name, mime_type
             FROM media_assets
             WHERE project_id = :project_id AND mime_type LIKE 'image/%'
             ORDER BY created_at ASC, id ASC
             LIMIT 1"
        );
        $statement->execute(['project_id' => $projectId]);
        $cover = $statement->fetch();
        return $cover === false ? null : $cover;
    }

    /**
     * Menyimpan atau menghapus gambar sampul khusus proyek.
     *
     * @return string|null nama berkas sampul sebelumnya agar pemanggil bisa
     *                     menghapus berkas lamanya dari disk
     */
    public function updateProjectCover(int $userId, int $projectId, ?string $storedName): ?string
    {
        $find = $this->database->prepare(
            'SELECT cover_filename FROM studio_projects WHERE id = :id AND user_id = :user_id'
        );
        $find->execute(['id' => $projectId, 'user_id' => $userId]);
        $previous = $find->fetchColumn();

        if ($previous === false) {
            return null;
        }

        $update = $this->database->prepare(
            'UPDATE studio_projects SET cover_filename = :cover_filename WHERE id = :id AND user_id = :user_id'
        );
        $update->bindValue('cover_filename', $storedName, $storedName === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $update->bindValue('id', $projectId, PDO::PARAM_INT);
        $update->bindValue('user_id', $userId, PDO::PARAM_INT);
        $update->execute();

        return is_string($previous) && $previous !== '' ? $previous : null;
    }

    /** @return array<string, mixed>|null */
    public function findPublishedProject(int $projectId): ?array
    {
        $statement = $this->database->prepare(
            "SELECT p.*, u.display_name, u.username, u.headline
             FROM studio_projects p
             INNER JOIN users u ON u.id = p.user_id
             WHERE p.id = :id AND p.status = 'published' AND p.is_hidden = 0
             LIMIT 1"
        );
        $statement->execute(['id' => $projectId]);
        $project = $statement->fetch();

        return $project === false ? null : $project;
    }

    /** @return array<string, int> */
    public function studioCounts(int $userId): array
    {
        $statement = $this->database->prepare("SELECT COUNT(*) AS total, SUM(status = 'published') AS published, SUM(status = 'draft') AS drafts, SUM(status = 'archived') AS archived FROM studio_projects WHERE user_id = :user_id");
        $statement->execute(['user_id' => $userId]);
        $counts = $statement->fetch() ?: [];
        return ['total' => (int) ($counts['total'] ?? 0), 'published' => (int) ($counts['published'] ?? 0), 'drafts' => (int) ($counts['drafts'] ?? 0), 'archived' => (int) ($counts['archived'] ?? 0)];
    }

    /** @return array<string, int> */
    public function monitoringCounts(): array
    {
        return [
            'creators' => (int) $this->database->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'projects' => (int) $this->database->query('SELECT COUNT(*) FROM studio_projects')->fetchColumn(),
            'published' => (int) $this->database->query("SELECT COUNT(*) FROM studio_projects WHERE status = 'published'")->fetchColumn(),
            'hidden' => (int) $this->database->query('SELECT COUNT(*) FROM studio_projects WHERE is_hidden = 1')->fetchColumn(),
            'inactive' => (int) $this->database->query('SELECT COUNT(*) FROM users WHERE is_active = 0')->fetchColumn(),
            'assets' => (int) $this->database->query('SELECT COUNT(*) FROM media_assets')->fetchColumn(),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function recentCreatorActivity(): array
    {
        return $this->database->query('SELECT u.id, u.display_name, u.username, u.email, u.is_active, COUNT(p.id) AS project_count, MAX(p.updated_at) AS last_activity FROM users u LEFT JOIN studio_projects p ON p.user_id = u.id GROUP BY u.id ORDER BY last_activity DESC, u.created_at DESC LIMIT 20')->fetchAll();
    }

    /**
     * Daftar pengguna untuk halaman monitoring superadmin.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listUsers(string $search = '', string $status = 'all', int $limit = 50, int $offset = 0): array
    {
        [$where, $params] = $this->userFilters($search, $status);

        $statement = $this->database->prepare(
            'SELECT u.id, u.name, u.email, u.display_name, u.username, u.headline, u.is_active, u.created_at,
                    (SELECT COUNT(*) FROM studio_projects p WHERE p.user_id = u.id) AS project_count,
                    (SELECT COUNT(*) FROM studio_projects p WHERE p.user_id = u.id AND p.status = \'published\' AND p.is_hidden = 0) AS published_count
             FROM users u
             WHERE ' . $where . '
             ORDER BY u.created_at DESC, u.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $this->bindParams($statement, $params);
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function countUsers(string $search = '', string $status = 'all'): int
    {
        [$where, $params] = $this->userFilters($search, $status);
        $statement = $this->database->prepare('SELECT COUNT(*) FROM users u WHERE ' . $where);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public function setUserActive(int $userId, bool $active): void
    {
        $statement = $this->database->prepare('UPDATE users SET is_active = :is_active WHERE id = :id');
        $statement->bindValue('is_active', $active ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue('id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Seluruh proyek dari semua creator, untuk halaman konten superadmin.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAllProjects(string $search = '', string $status = 'all', string $visibility = 'all', int $limit = 60, int $offset = 0): array
    {
        [$where, $params] = $this->adminProjectFilters($search, $status, $visibility);

        $statement = $this->database->prepare(
            'SELECT p.id, p.title, p.category, p.status, p.is_hidden, p.featured, p.updated_at,
                    u.id AS user_id, u.display_name, u.username, u.is_active,
                    (SELECT COUNT(*) FROM media_assets m WHERE m.project_id = p.id) AS media_count
             FROM studio_projects p
             INNER JOIN users u ON u.id = p.user_id
             WHERE ' . $where . '
             ORDER BY p.is_hidden DESC, p.updated_at DESC, p.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $this->bindParams($statement, $params);
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $projects = $statement->fetchAll();

        foreach ($projects as $index => $project) {
            $projects[$index]['cover'] = $this->findProjectCover((int) $project['id']);
        }

        return $projects;
    }

    public function countAllProjects(string $search = '', string $status = 'all', string $visibility = 'all'): int
    {
        [$where, $params] = $this->adminProjectFilters($search, $status, $visibility);
        $statement = $this->database->prepare(
            'SELECT COUNT(*)
             FROM studio_projects p
             INNER JOIN users u ON u.id = p.user_id
             WHERE ' . $where
        );
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public function setProjectHidden(int $projectId, bool $hidden): void
    {
        $statement = $this->database->prepare('UPDATE studio_projects SET is_hidden = :is_hidden WHERE id = :id');
        $statement->bindValue('is_hidden', $hidden ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue('id', $projectId, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Filter bersama untuk daftar pengguna superadmin.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function userFilters(string $search, string $status): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($status === 'active') {
            $where[] = 'u.is_active = 1';
        } elseif ($status === 'inactive') {
            $where[] = 'u.is_active = 0';
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where[] = '(u.display_name LIKE :search_name OR u.username LIKE :search_username OR u.email LIKE :search_email OR u.name LIKE :search_full)';
            $params['search_name'] = $like;
            $params['search_username'] = $like;
            $params['search_email'] = $like;
            $params['search_full'] = $like;
        }

        return [implode(' AND ', $where), $params];
    }

    /**
     * Filter bersama untuk daftar proyek superadmin.
     *
     * @return array{0: string, 1: array<string, string>}
     */
    private function adminProjectFilters(string $search, string $status, string $visibility): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (in_array($status, ['draft', 'published', 'archived'], true)) {
            $where[] = 'p.status = :status';
            $params['status'] = $status;
        }

        if ($visibility === 'hidden') {
            $where[] = 'p.is_hidden = 1';
        } elseif ($visibility === 'visible') {
            $where[] = 'p.is_hidden = 0';
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $where[] = '(p.title LIKE :search_title OR p.category LIKE :search_category OR u.display_name LIKE :search_name OR u.username LIKE :search_username)';
            $params['search_title'] = $like;
            $params['search_category'] = $like;
            $params['search_name'] = $like;
            $params['search_username'] = $like;
        }

        return [implode(' AND ', $where), $params];
    }

    /** @param array<string, string> $params */
    private function bindParams(\PDOStatement $statement, array $params): void
    {
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
    }

    private function makeAvailableUsername(string $name): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $slugSource = strtolower($transliterated === false ? '' : $transliterated);
        $base = trim((string) preg_replace('/[^a-z0-9]+/', '-', $slugSource), '-');
        $base = $base === '' ? 'creator' : substr($base, 0, 50);
        $username = $base;
        $number = 2;

        while ($this->usernameExists($username)) {
            $username = $base . '-' . $number++;
        }

        return $username;
    }
}
