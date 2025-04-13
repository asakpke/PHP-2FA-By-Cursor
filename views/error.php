<?php
$title = 'Error';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Error</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        ' . htmlspecialchars($error ?? 'An unknown error occurred.') . '
                    </div>
                    <a href="/" class="btn btn-primary">Return to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
';

require __DIR__ . '/layouts/main.php'; 