<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Memvalidasi dan menyimpan berkas upload secara terpusat.
 * Tidak mempercayai ekstensi maupun MIME type dari request browser.
 */
final class MediaUploadService
{
    /** @param array{directory: string, max_size: int, allowed_mime_types: array<string, string>} $config */
    public function __construct(private array $config)
    {
    }

    /**
     * @param array<string, mixed>|null $file
     * @return array{original_name: string, stored_name: string, mime_type: string, file_size: int}
     */
    public function store(?array $file): array
    {
        $this->validateUpload($file);

        $temporaryPath = (string) $file['tmp_name'];
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
        $extension = $this->config['allowed_mime_types'][$mimeType] ?? null;

        if ($extension === null) {
            throw new RuntimeException(
                'Hanya JPG, PNG, WebP, GIF, MP4, WebM, dan PDF yang dapat diunggah.'
            );
        }

        $this->createDirectoryIfMissing();
        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $this->config['directory'] . '/' . $storedName;

        if (!move_uploaded_file($temporaryPath, $targetPath)) {
            throw new RuntimeException('Berkas tidak dapat disimpan. Silakan coba lagi.');
        }

        return [
            'original_name' => basename((string) $file['name']),
            'stored_name' => $storedName,
            'mime_type' => $mimeType,
            'file_size' => (int) $file['size'],
        ];
    }

    public function remove(string $storedName): void
    {
        $filePath = $this->config['directory'] . '/' . basename($storedName);

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    /** @param array<string, mixed>|null $file */
    private function validateUpload(?array $file): void
    {
        if ($file === null) {
            throw new RuntimeException('Pilih berkas yang ingin diunggah.');
        }

        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException(
                'Berkas ditolak oleh konfigurasi PHP karena terlalu besar. Pastikan batas upload PHP minimal 25 MB.'
            );
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gagal. Silakan pilih berkas dan coba lagi.');
        }

        if ((int) $file['size'] > $this->config['max_size']) {
            throw new RuntimeException('Ukuran berkas maksimal adalah 25 MB.');
        }
    }

    private function createDirectoryIfMissing(): void
    {
        if (!is_dir($this->config['directory'])) {
            mkdir($this->config['directory'], 0755, true);
        }
    }
}
