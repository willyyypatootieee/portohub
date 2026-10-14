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

        $savedId = $this->repository->saveProject($userId, $project, $projectId ?: null);

        // Proyek baru boleh menyertakan berkas media langsung dari form.
        if ($projectId === 0) {
            $result = $this->storeUploadedMedia(
                $userId,
                $savedId,
                $this->normalizeUploadedFiles($_FILES['portfolio_files'] ?? null)
            );

            if ($result['errors'] !== []) {
                setFlash('error', 'Proyek dibuat, tetapi sebagian media gagal diunggah: ' . implode(' | ', $result['errors']));
            } else {
                $message = 'Proyek berhasil dibuat.';
                if ($result['count'] > 0) {
                    $message .= ' ' . $result['count'] . ' berkas media berhasil diunggah.';
                }
                setFlash('success', $message);
            }

            redirect(appUrl('/studio/projects.php?edit=' . $savedId . '#project-media'));
        }

        setFlash('success', 'Proyek berhasil diperbarui.');
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

        $result = $this->storeUploadedMedia(
            $userId,
            $projectId,
            $this->normalizeUploadedFiles($_FILES['portfolio_files'] ?? null)
        );

        if ($result['count'] > 0) {
            setFlash(
                'success',
                $result['count'] === 1
                    ? 'Media "' . $result['last'] . '" berhasil diunggah.'
                    : $result['count'] . ' berkas media berhasil diunggah.'
            );
        }

        return $result['errors'];
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

    public function deleteGalleryMedia(int $userId): void
    {
        $media = $this->repository->deleteMedia($userId, (int) ($_POST['id'] ?? 0));

        if ($media !== null) {
            $this->mediaUploader->remove((string) $media['stored_name']);
            setFlash('success', 'Media berhasil dihapus.');
        }

        redirect(appUrl('/studio/projects.php#media-gallery'));
    }

    /**
     * Unggah atau ganti CV creator (hanya PDF). Berkas disimpan di luar web
     * root dan hanya bisa dilihat lewat route /studio/cv.php.
     */
    public function uploadCv(int $userId): ?string
    {
        $uploader = new MediaUploadService(cvUploadConfig());
        $storedName = null;

        try {
            $uploaded = $uploader->store($_FILES['cv_file'] ?? null);
            $storedName = $uploaded['stored_name'];
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }

        $previous = $this->repository->findUser($userId);
        $this->repository->updateCv($userId, $storedName, $uploaded['original_name']);

        if ($previous !== null && !empty($previous['cv_filename'])) {
            $uploader->remove((string) $previous['cv_filename']);
        }

        setFlash('success', 'CV berhasil diunggah.');
        redirect(appUrl('/studio/settings.php#cv'));
    }

    public function deleteCv(int $userId): void
    {
        $user = $this->repository->findUser($userId);

        if ($user !== null && !empty($user['cv_filename'])) {
            (new MediaUploadService(cvUploadConfig()))->remove((string) $user['cv_filename']);
        }

        $this->repository->clearCv($userId);
        setFlash('success', 'CV berhasil dihapus.');
        redirect(appUrl('/studio/settings.php#cv'));
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
     * Menyimpan daftar berkas media ke sebuah proyek. Mengembalikan jumlah
     * berkas yang berhasil dan pesan error per berkas agar pemanggil bisa
     * menentukan flash message dan tampilan yang tepat.
     *
     * @param array<int, array<string, mixed>> $files
     * @return array{count: int, last: string|null, errors: array<int, string>}
     */
    private function storeUploadedMedia(int $userId, int $projectId, array $files): array
    {
        $count = 0;
        $last = null;
        $errors = [];

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
                $count++;
                $last = $uploadedMedia['original_name'];
            } catch (RuntimeException $exception) {
                $errors[] = basename((string) ($file['name'] ?? 'berkas')) . ': ' . $exception->getMessage();
            }
        }

        return ['count' => $count, 'last' => $last, 'errors' => $errors];
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
