<?php 
$title = 'Welcome to PHP Auth App';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Welcome</div>
                <div class="card-body">
                    <h2>Welcome to PHP 2FA By Cursor</h2>
                    <p>This is a simple authentication system built with PHP that includes two-factor authentication for enhanced security.</p>
                    <p>Features include:</p>
                    <ul>
                        <li>User registration and login</li>
                        <li>Email verification</li>
                        <li>Two-factor authentication via email or SMS</li>
                        <li>Activity logging for security monitoring</li>
                    </ul>
                    <p>Get started by logging in or registering for an account!</p>
                    ' . (!isset($_SESSION['user_id']) ? '
                    <div class="mt-4">
                        <a href="/login" class="btn btn-primary me-2">Login</a>
                        <a href="/register" class="btn btn-secondary">Register</a>
                    </div>
                    ' : '') . '
                </div>
            </div>
        </div>
    </div>
</div>
';

require __DIR__ . '/layouts/main.php'; 