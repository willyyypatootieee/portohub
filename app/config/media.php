<?php

declare(strict_types=1);

/**
 * Konfigurasi upload creator studio.
 *
 * Kunci array adalah MIME type hasil pemeriksaan finfo, bukan nilai dari
 * browser. Ini mencegah pengguna menyamarkan jenis file lewat ekstensi.
 *
 * @return array{directory: string, max_size: int, allowed_mime_types: array<string, string>}
 */
function mediaUploadConfig(): array
{
    return [
        'directory' => dirname(__DIR__, 2) . '/assets/uploads',
        'max_size' => 25 * 1024 * 1024,
        'allowed_mime_types' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'application/pdf' => 'pdf',
        ],
    ];
}
