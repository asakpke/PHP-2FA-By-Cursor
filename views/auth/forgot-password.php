<?php
$title = 'Forgot Password';
$content = '
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Reset Password</div>
            <div class="card-body">
                <form method="POST" action="/forgot-password">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="form-text">We\'ll send you a link to reset your password.</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="/login" class="text-decoration-none">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
';

require __DIR__ . '/../layouts/main.php'; 