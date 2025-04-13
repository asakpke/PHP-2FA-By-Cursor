<?php
if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: /login');
    exit;
}

$title = '2FA Verification';
$content = '
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/2fa/verify" onsubmit="return validateCode()">
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
                            <div class="form-text">Enter the verification code from your authenticator app</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Verify</button>
                            <a href="/logout" class="btn btn-secondary">Cancel</a>
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