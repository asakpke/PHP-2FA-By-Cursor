<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\TwoFactorAuth;
use App\Models\ActivityLog;
use Exception;

class AuthController {
    private User $userModel;
    private TwoFactorAuth $twoFactorAuth;
    private ActivityLog $activityLog;
    
    public function __construct(
        User $userModel, 
        TwoFactorAuth $twoFactorAuth,
        ActivityLog $activityLog
    ) {
        $this->userModel = $userModel;
        $this->twoFactorAuth = $twoFactorAuth;
        $this->activityLog = $activityLog;
    }
    
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            // Validate input
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            if (empty($name)) {
                $errors[] = "Name is required";
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Valid email is required";
            }
            
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters";
            }
            
            if (empty($errors)) {
                if ($this->userModel->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ])) {
                    $_SESSION['success'] = "Registration successful! Please login.";
                    header('Location: /login');
                    exit;
                }
                $errors[] = "Registration failed";
            }
            
            $_SESSION['errors'] = $errors;
        }
        
        require_once __DIR__ . '/../../views/auth/register.php';
    }
    
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // If 2FA is enabled, redirect to 2FA verification
                if ($user['two_factor_secret']) {
                    $_SESSION['2fa_user_id'] = $user['id'];
                    header('Location: /2fa/verify');
                    exit;
                }
                
                // If no 2FA, proceed with normal login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Log the login activity
                $this->activityLog->logLogin($user['id']);
                
                header('Location: /dashboard');
                exit;
            }
            
            $errors[] = "Invalid credentials";
            $_SESSION['errors'] = $errors;
        }
        
        require_once __DIR__ . '/../../views/auth/login.php';
    }
    
    public function logout(): void {
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    public function setup2FA(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        // Add debug logging
        error_log("User 2FA status: " . ($user['two_factor_secret'] ? 'Enabled' : 'Disabled'));
        
        // Check if 2FA is already enabled
        if ($user['two_factor_secret']) {
            $_SESSION['info'] = "Two-factor authentication is already enabled.";
            error_log("2FA already enabled, redirecting to dashboard");
            header('Location: /dashboard');
            exit;
        }
        
        // Generate secret only if not already in session
        if (!isset($_SESSION['2fa_temp_secret'])) {
            $_SESSION['2fa_temp_secret'] = $this->twoFactorAuth->generateSecret();
        }
        $secret = $_SESSION['2fa_temp_secret'];
        
        $qrCodeUrl = $this->twoFactorAuth->getQRCodeUrl($user['email'], $secret);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            
            // Validate code format
            if (!preg_match('/^\d{6}$/', $code)) {
                $_SESSION['errors'] = ["Invalid code format. Please enter 6 digits."];
            } else {
                // Log verification attempt
                error_log("2FA Verification attempt:");
                error_log("User ID: {$_SESSION['user_id']}");
                error_log("User Email: {$user['email']}");
                error_log("Secret: $secret");
                error_log("Code: $code");
                
                if ($this->twoFactorAuth->verifyCode($secret, $code)) {
                    if ($this->userModel->enable2FA($_SESSION['user_id'], $secret)) {
                        // Clear the temporary secret
                        unset($_SESSION['2fa_temp_secret']);
                        $_SESSION['success'] = "Two-factor authentication enabled successfully!";
                        header('Location: /dashboard');
                        exit;
                    } else {
                        $_SESSION['errors'] = ["Failed to save 2FA settings."];
                    }
                } else {
                    $_SESSION['errors'] = ["Invalid verification code. Please try again."];
                }
            }
        }
        
        extract([
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret
        ]);
        
        require __DIR__ . '/../../views/auth/2fa-setup.php';
    }

    public function verify2FA(): void {
        if (!isset($_SESSION['2fa_user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $user = $this->userModel->findById($_SESSION['2fa_user_id']);
            
            if ($this->twoFactorAuth->verifyCode($user['two_factor_secret'], $code)) {
                // Clear 2FA session and set normal session
                unset($_SESSION['2fa_user_id']);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Log the login activity
                $this->activityLog->logLogin($user['id']);
                
                header('Location: /dashboard');
                exit;
            }
            
            $_SESSION['errors'] = ["Invalid verification code"];
        }
        
        require __DIR__ . '/../../views/auth/2fa-verify.php';
    }

    public function manage2FA(): void {
        error_log("Accessing manage2FA method");
        
        if (!isset($_SESSION['user_id'])) {
            error_log("No user session, redirecting to login");
            header('Location: /login');
            exit;
        }
        
        try {
            $user = $this->userModel->findById($_SESSION['user_id']);
            error_log("Found user: " . json_encode($user));
            
            if (!$user) {
                error_log("User not found");
                throw new Exception("User not found");
            }
            
            require __DIR__ . '/../../views/auth/2fa-manage.php';
        } catch (Exception $e) {
            error_log("Error in manage2FA: " . $e->getMessage());
            $_SESSION['errors'] = ["An error occurred while managing 2FA settings."];
            header('Location: /dashboard');
            exit;
        }
    }

    public function disable2FA(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->disable2FA($_SESSION['user_id'])) {
                $_SESSION['success'] = "Two-factor authentication has been disabled.";
            } else {
                $_SESSION['errors'] = ["Failed to disable 2FA."];
            }
        }
        
        header('Location: /2fa/manage');
        exit;
    }
} 