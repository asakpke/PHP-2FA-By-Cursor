<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$title = '2FA Management';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    ' . ($user['two_factor_secret'] ? '
                    <div class="alert alert-success">
                        <i class="bi bi-shield-check"></i> Two-factor authentication is currently enabled.
                    </div>
                    <form method="POST" action="/2fa/disable" onsubmit="return confirm(\'Are you sure you want to disable 2FA? This will make your account less secure.\')">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger">Disable 2FA</button>
                        </div>
                    </form>
                    ' : '
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-exclamation"></i> Two-factor authentication is currently disabled.
                    </div>
                    <div class="d-grid">
                        <a href="/2fa/setup" class="btn btn-primary">Enable 2FA</a>
                    </div>
                    ') . '
                </div>
            </div>
        </div>
    </div>
</div>
';

require __DIR__ . '/../layouts/main.php'; 