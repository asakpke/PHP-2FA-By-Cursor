<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

if (!isset($qrCodeUrl) || !isset($secret)) {
    $_SESSION['errors'] = ["Error initializing 2FA setup"];
    header('Location: /dashboard');
    exit;
}

$title = '2FA Setup';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Setup Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Setup Instructions:</h5>
                        <ol>
                            <li>Install Google Authenticator or any other 2FA app on your phone</li>
                            <li>Scan the QR code or enter the secret key manually</li>
                            <li>Enter the verification code from your app to confirm setup</li>
                        </ol>
                    </div>
                    
                    <div class="text-center mb-4">
                        <img src="' . htmlspecialchars($qrCodeUrl) . '" 
                            class="img-fluid" alt="QR Code"
                            style="border: 1px solid #ddd; padding: 10px; max-width: 300px;">
                    </div>
                    
                    <div class="mb-4 text-center">
                        <p>Secret Key (if QR code cannot be scanned):</p>
                        <code class="p-2 bg-light d-inline-block">' . chunk_split($secret, 4, ' ') . '</code>
                    </div>
                    
                    <form method="POST" action="/2fa/setup" onsubmit="return validateCode()">
                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" 
                                class="form-control" 
                                id="code" 
                                name="code" 
                                required 
                                placeholder="Enter the 6-digit code from your app"
                                pattern="[0-9]{6}" 
                                title="Please enter a 6-digit code"
                                autocomplete="off"
                                oninput="this.value = this.value.replace(/[^0-9]/g, \'\').substring(0, 6)">
                            <div class="form-text">Enter the 6-digit code shown in your authenticator app</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Verify and Enable 2FA</button>
                            <a href="/dashboard" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateCode() {
    var code = document.getElementById("code").value;
    if (!/^\d{6}$/.test(code)) {
        alert("Please enter a valid 6-digit code");
        return false;
    }
    return true;
}
</script>
';

require __DIR__ . '/../layouts/main.php'; 