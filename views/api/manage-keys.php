<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$title = 'API Keys';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">API Keys</h4>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="generate">
                        <button type="submit" class="btn btn-primary btn-sm">Generate New Key</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> API keys allow external applications to access our API on your behalf.
                    </div>
                    
                    ' . (isset($newApiKey) ? '
                    <div class="alert alert-success">
                        <h5>New API Key Generated</h5>
                        <p class="mb-2">Copy your new API key now. You won\'t be able to see it again!</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="' . htmlspecialchars($newApiKey) . '" id="newApiKey" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                                Copy
                            </button>
                        </div>
                    </div>
                    ' : '') . '
                    
                    ' . (empty($apiKeys) ? '
                    <div class="text-center py-4">
                        <p class="text-muted">You haven\'t generated any API keys yet.</p>
                    </div>
                    ' : '
                    <div class="list-group">
                        ' . implode('', array_map(function($key) {
                            return '
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">API Key: ' . substr($key['api_key'], 0, 8) . '...</h6>
                                        <small class="text-muted">Created: ' . $key['created_at'] . '</small>
                                    </div>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="revoke">
                                        <input type="hidden" name="key_id" value="' . $key['id'] . '">
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm(\'Are you sure you want to revoke this API key?\')">
                                            Revoke
                                        </button>
                                    </form>
                                </div>
                            </div>';
                        }, $apiKeys)) . '
                    </div>
                    ') . '
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyApiKey() {
    var copyText = document.getElementById("newApiKey");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand("copy");
    
    // Optional: Show feedback
    var button = copyText.nextElementSibling;
    var originalText = button.innerHTML;
    button.innerHTML = "Copied!";
    setTimeout(function() {
        button.innerHTML = originalText;
    }, 2000);
}
</script>
';

require __DIR__ . '/../layouts/main.php'; 