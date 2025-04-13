<?php
$title = 'Login';
$content = '
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    ' . (isset($_SESSION['2fa_required']) ? '
                    <div class="mb-3">
                        <label for="2fa_code" class="form-label">2FA Code</label>
                        <input type="text" class="form-control" id="2fa_code" name="2fa_code" required>
                    </div>
                    ' : '') . '
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="/forgot-password" class="text-decoration-none">Forgot Password?</a>
                        <span class="mx-2">|</span>
                        <a href="/register" class="text-decoration-none">Need an account? Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
';

require __DIR__ . '/../layouts/main.php'; 