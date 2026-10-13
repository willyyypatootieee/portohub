<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\JsonFormValidator;
use App\Repositories\StudioRepository;
use App\Services\MediaUploadService;
use RuntimeException;

final class StudioController
{
    public function __construct(
        private StudioRepository $repository,
        private JsonFormValidator $validator,
        private MediaUploadService $mediaUploader
    ) {
    }

    /** @return array<string, string> */
    public function saveProject(int $userId): array
    {
        $projectId = (int) ($_POST['id'] ?? 0);
        $project = $this->projectFromRequest();
        $errors = $this->validator->validate('project', $project);

        if ($errors !== []) {
            return $errors;
        }

        $this->repository->saveProject($userId, $project, $projectId ?: null);
        setFlash('success', $projectId > 0 ? 'Proyek berhasil diperbarui.' : 'Proyek berhasil dibuat.');
        redirect(appUrl('/studio/projects.php'));
    }

    public function deleteProject(int $userId): void
    {
        $this->repository->deleteProject($userId, (int) ($_POST['id'] ?? 0));
        setFlash('success', 'Proyek berhasil dihapus.');
        redirect(appUrl('/studio/projects.php'));
    }

    /**
     * Menangani upload media untuk satu proyek dari halaman edit proyek.
     * Mendukung beberapa berkas sekaligus lewat input portfolio_files[].
     *
     * @return array<int, string> daftar pesan error
     */
    public function uploadProjectMedia(int $userId): array
    {
        $projectId = (int) ($_POST['project_id'] ?? 0);
        $project = $projectId > 0 ? $this->repository->findProject($userId, $projectId) : null;

        if ($project === null) {
            return ['Pilih proyek milik Anda sendiri sebelum mengunggah media.'];
        }

        $files = $this->normalizeUploadedFiles($_FILES['portfolio_files'] ?? null);
        $errors = [];
        $uploadedCount = 0;
        $lastUploadedName = null;

        foreach ($files as $file) {
            try {
                $uploadedMedia = $this->mediaUploader->store($file);
                $this->repository->addMedia(
                    $userId,
                    $projectId,
                    $uploadedMedia['original_name'],
                    $uploadedMedia['stored_name'],
                    $uploadedMedia['mime_type'],
                    $uploadedMedia['file_size']
                );
                $uploadedCount++;
                $lastUploadedName = $uploadedMedia['original_name'];
            } catch (RuntimeException $exception) {
                $errors[] = basename((string) ($file['name'] ?? 'berkas')) . ': ' . $exception->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            setFlash(
                'success',
                $uploadedCount === 1
                    ? 'Media "' . $lastUploadedName . '" berhasil diunggah.'
                    : $uploadedCount . ' berkas media berhasil diunggah.'
            );
        }

        return $errors;
    }

    public function deleteProjectMedia(int $userId): void
    {
        $projectId = (int) ($_POST['project_id'] ?? 0);
        $media = $this->repository->deleteMedia($userId, (int) ($_POST['id'] ?? 0));

        if ($media !== null) {
            $this->mediaUploader->remove((string) $media['stored_name']);
            setFlash('success', 'Media berhasil dihapus.');
        }

        redirect(appUrl('/studio/projects.php?edit=' . $projectId . '#project-media'));
    }

    public function uploadMedia(int $userId): ?string
    {
        $projectId = (int) ($_POST['project_id'] ?? 0);

        if ($projectId > 0 && $this->repository->findProject($userId, $projectId) === null) {
            return 'Pilih proyek milik Anda sendiri.';
        }

        try {
            $uploadedMedia = $this->mediaUploader->store($_FILES['portfolio_file'] ?? null);
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }

        $this->repository->addMedia(
            $userId,
            $projectId ?: null,
            $uploadedMedia['original_name'],
            $uploadedMedia['stored_name'],
            $uploadedMedia['mime_type'],
            $uploadedMedia['file_size']
        );

        setFlash('success', 'Media berhasil diunggah.');
        redirect(appUrl('/studio/media.php'));
    }

    public function deleteMedia(int $userId): void
    {
        $media = $this->repository->deleteMedia($userId, (int) ($_POST['id'] ?? 0));

        if ($media !== null) {
            $this->mediaUploader->remove((string) $media['stored_name']);
            setFlash('success', 'Media berhasil dihapus.');
        }

        redirect(appUrl('/studio/media.php'));
    }

    /** @return array<string, string> */
    public function updateProfile(int $userId): array
    {
        $profile = [
            'display_name' => trim((string) ($_POST['display_name'] ?? '')),
            'username' => strtolower(trim((string) ($_POST['username'] ?? ''))),
            'headline' => trim((string) ($_POST['headline'] ?? '')),
            'bio' => trim((string) ($_POST['bio'] ?? '')),
        ];
        $errors = $this->validator->validate('profile', $profile);

        if ($this->repository->usernameExists($profile['username'], $userId)) {
            $errors['username'] = 'Username ini sudah digunakan.';
        }

        if ($errors === []) {
            $this->repository->updateProfile($userId, $profile);
            setFlash('success', 'Perubahan profil berhasil disimpan.');
            redirect(appUrl('/studio/settings.php'));
        }

        return $errors;
    }

    /** @return array{title: string, category: string, description: string, status: string, featured: int} */
    private function projectFromRequest(): array
    {
        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'draft'),
            'featured' => isset($_POST['featured']) ? 1 : 0,
        ];
    }

    /**
     * Mengubah struktur $_FILES multi-upload menjadi daftar berkas sederhana
     * agar mudah diproses satu per satu.
     *
     * @param array<string, mixed>|null $files
     * @return array<int, array<string, mixed>>
     */
    private function normalizeUploadedFiles(?array $files): array
    {
        if ($files === null || !isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $normalized = [];

        foreach ($files['name'] as $index => $name) {
            if ((int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }
}
