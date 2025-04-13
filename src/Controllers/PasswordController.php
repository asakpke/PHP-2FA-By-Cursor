<?php
declare(strict_types=1);

namespace App\Controllers;

class PasswordController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    public function forgot(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $token = bin2hex(random_bytes(32));
            
            if ($this->userModel->setResetToken($email, $token)) {
                // Send reset email
                $resetLink = $_ENV['APP_URL'] . "/reset-password?token=" . $token;
                // Implement email sending logic
            }
        }
        require __DIR__ . '/../../views/auth/forgot-password.php';
    }
} 