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
    /**
     * @param array{
     *     directory: string,
     *     max_size: int,
     *     max_size_label?: string,
     *     allowed_label?: string,
     *     allowed_mime_types: array<string, string>
     * } $config
     */
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
                'Jenis berkas tidak diizinkan. Hanya ' . $this->allowedLabel() . ' yang dapat diunggah.'
            );
        }

        $this->createDirectoryIfMissing();
        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetPath = $this->config['directory'] . '/' . $storedName;

        // Panggilan ini memancarkan warning PHP bila gagal (mis. izin folder).
        // Warning disembunyikan agar pengguna menerima pesan yang jelas, dan
        // penyebabnya dicatat supaya mudah didiagnosis.
        if (!@move_uploaded_file($temporaryPath, $targetPath)) {
            $reason = error_get_last()['message'] ?? 'penyebab tidak diketahui';
            error_log('PortfolioHub: gagal menyimpan upload ke ' . $targetPath . ' — ' . $reason);

            throw new RuntimeException(
                'Berkas tidak dapat disimpan. Pastikan folder penyimpanan dapat ditulis '
                . 'oleh web server, lalu coba lagi.'
            );
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
                'Berkas ditolak oleh konfigurasi PHP karena terlalu besar. Pastikan batas upload PHP minimal '
                . $this->maxSizeLabel() . '.'
            );
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gagal. Silakan pilih berkas dan coba lagi.');
        }

        if ((int) $file['size'] > $this->config['max_size']) {
            throw new RuntimeException('Ukuran berkas maksimal adalah ' . $this->maxSizeLabel() . '.');
        }
    }

    private function maxSizeLabel(): string
    {
        return (string) ($this->config['max_size_label'] ?? '25 MB');
    }

    private function allowedLabel(): string
    {
        return (string) ($this->config['allowed_label'] ?? 'JPG, PNG, WebP, GIF, MP4, WebM, dan PDF');
    }

    private function createDirectoryIfMissing(): void
    {
        $directory = $this->config['directory'];

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException(
                'Folder penyimpanan "' . $directory . '" tidak dapat dibuat. '
                . 'Pastikan folder storage/ dapat ditulis oleh pengguna web server.'
            );
        }

        if (!is_writable($directory)) {
            throw new RuntimeException(
                'Folder penyimpanan "' . $directory . '" tidak dapat ditulis oleh web server. '
                . 'Di XAMPP/Laragon, izinkan akses tulis pada folder tersebut '
                . '(lihat README bagian "Izin folder upload").'
            );
        }
    }
}
