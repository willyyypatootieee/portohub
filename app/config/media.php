<?php

declare(strict_types=1);

/**
 * Konfigurasi upload creator studio.
 *
 * Kunci array allowed_mime_types adalah MIME type hasil pemeriksaan finfo,
 * bukan nilai dari browser. Ini mencegah pengguna menyamarkan jenis file lewat
 * ekstensi. Label teks dipakai MediaUploadService untuk menyusun pesan error
 * yang ramah dibaca.
 *
 * @return array{
 *     directory: string,
 *     max_size: int,
 *     max_size_label: string,
 *     allowed_label: string,
 *     allowed_mime_types: array<string, string>
 * }
 */
function mediaUploadConfig(): array
{
    return [
        'directory' => dirname(__DIR__, 2) . '/assets/uploads',
        'max_size' => 25 * 1024 * 1024,
        'max_size_label' => '25 MB',
        'allowed_label' => 'JPG, PNG, WebP, GIF, MP4, WebM, dan PDF',
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

/**
 * Konfigurasi upload CV.
 *
 * CV hanya boleh PDF dan disimpan di luar web root (storage/cv) supaya tidak
 * bisa diakses lewat URL langsung. Preview dan unduhan dilayani route
 * /studio/cv.php milik creator yang sedang masuk.
 *
 * @return array{
 *     directory: string,
 *     max_size: int,
 *     max_size_label: string,
 *     allowed_label: string,
 *     allowed_mime_types: array<string, string>
 * }
 */
function cvUploadConfig(): array
{
    return [
        'directory' => dirname(__DIR__, 2) . '/storage/cv',
        'max_size' => 10 * 1024 * 1024,
        'max_size_label' => '10 MB',
        'allowed_label' => 'PDF',
        'allowed_mime_types' => [
            'application/pdf' => 'pdf',
        ],
    ];
}

/**
 * Konfigurasi upload gambar sampul proyek.
 *
 * Sampul wajib berupa gambar supaya grid portofolio publik selalu punya
 * pratinjau yang seragam. Berkas disimpan bersama media lain di
 * assets/uploads agar bisa disajikan langsung lewat URL.
 *
 * @return array{
 *     directory: string,
 *     max_size: int,
 *     max_size_label: string,
 *     allowed_label: string,
 *     allowed_mime_types: array<string, string>
 * }
 */
function projectCoverUploadConfig(): array
{
    return [
        'directory' => dirname(__DIR__, 2) . '/assets/uploads',
        'max_size' => 10 * 1024 * 1024,
        'max_size_label' => '10 MB',
        'allowed_label' => 'JPG, PNG, WebP, dan GIF',
        'allowed_mime_types' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ],
    ];
}
