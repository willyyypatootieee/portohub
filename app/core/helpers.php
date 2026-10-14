<?php

declare(strict_types=1);

/**
 * Titik muat untuk seluruh helper aplikasi.
 *
 * Setiap topik dipisah ke berkas sendiri di folder helpers/ supaya mudah
 * dicari dan dirawat:
 *
 *   - env.php    : membaca berkas .env
 *   - output.php : escape output HTML
 *   - csrf.php   : token CSRF untuk form POST
 *   - url.php    : redirect dan penyusunan URL
 *   - flash.php  : pesan flash sekali tampil
 *   - auth.php   : status login tingkat halaman
 *   - format.php : format angka untuk tampilan
 *   - response.php : deteksi permintaan JSON dan balasan JSON
 */

require_once __DIR__ . '/helpers/env.php';
require_once __DIR__ . '/helpers/output.php';
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/helpers/url.php';
require_once __DIR__ . '/helpers/flash.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/format.php';
require_once __DIR__ . '/helpers/response.php';
