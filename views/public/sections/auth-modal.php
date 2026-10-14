<?php
/**
 * Modal login/register untuk landing page.
 *
 * Form sengaja tetap mengarah ke route asli /login/ dan /login/register/ agar
 * tetap berfungsi tanpa JavaScript. app.js menambahkan pengiriman lewat fetch
 * supaya pesan error tampil di dalam modal tanpa berpindah halaman.
 */
?>
<div class="auth-modal" data-auth-modal hidden>
    <div class="auth-modal-backdrop" data-auth-close></div>

    <div class="auth-modal-dialog" role="dialog" aria-modal="true" aria-label="Masuk atau daftar akun">
        <button class="auth-modal-close" type="button" aria-label="Tutup" data-auth-close>&times;</button>

        <div class="auth-modal-tabs" role="tablist" aria-label="Masuk atau daftar">
            <button class="auth-modal-tab is-active" type="button" role="tab" id="auth-tab-login" aria-selected="true" aria-controls="auth-panel-login" data-auth-tab="login">Masuk</button>
            <button class="auth-modal-tab" type="button" role="tab" id="auth-tab-register" aria-selected="false" aria-controls="auth-panel-register" data-auth-tab="register">Daftar</button>
        </div>

        <section class="auth-modal-panel" id="auth-panel-login" role="tabpanel" aria-labelledby="auth-tab-login" data-auth-panel="login">
            <h2>Selamat datang kembali</h2>
            <p class="auth-modal-intro">Masuk untuk mengelola portofolio Anda.</p>

            <form class="auth-form" method="post" action="<?= escape(appUrl('/login/')) ?>" novalidate data-auth-form>
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                <p class="auth-modal-alert" role="alert" hidden data-auth-alert></p>

                <label class="auth-field">
                    <span>Email</span>
                    <input type="email" name="email" autocomplete="email" required>
                </label>

                <label class="auth-field">
                    <span>Kata sandi</span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>

                <button class="button" type="submit">Masuk</button>
            </form>

            <p class="auth-modal-switch">Belum punya akun?
                <button type="button" data-auth-tab="register">Daftar sekarang</button>
            </p>
        </section>

        <section class="auth-modal-panel" id="auth-panel-register" role="tabpanel" aria-labelledby="auth-tab-register" data-auth-panel="register" hidden>
            <h2>Buat portofolio yang mudah diingat</h2>
            <p class="auth-modal-intro">Mulai dengan membuat akun creator Anda.</p>

            <form class="auth-form" method="post" action="<?= escape(appUrl('/login/register/')) ?>" novalidate data-auth-form>
                <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                <p class="auth-modal-alert" role="alert" hidden data-auth-alert></p>

                <label class="auth-field">
                    <span>Nama</span>
                    <input type="text" name="name" autocomplete="name" required>
                </label>

                <label class="auth-field">
                    <span>Email</span>
                    <input type="email" name="email" autocomplete="email" required>
                </label>

                <label class="auth-field">
                    <span>Kata sandi (minimal 8 karakter)</span>
                    <input type="password" name="password" autocomplete="new-password" required>
                </label>

                <label class="auth-field">
                    <span>Konfirmasi kata sandi</span>
                    <input type="password" name="password_confirmation" autocomplete="new-password" required>
                </label>

                <button class="button" type="submit">Buat akun</button>
            </form>

            <p class="auth-modal-switch">Sudah punya akun?
                <button type="button" data-auth-tab="login">Masuk</button>
            </p>
        </section>
    </div>
</div>
