<?php
declare(strict_types=1);

namespace App\Controllers;

class ProfileController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    public function edit(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'bio' => filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING),
            ];
            
            $errors = [];
            
            if (empty($data['name'])) {
                $errors[] = "Name is required";
            }
            
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Valid email is required";
            }
            
            if (empty($errors)) {
                if ($this->userModel->updateProfile($_SESSION['user_id'], $data)) {
                    $_SESSION['success'] = "Profile updated successfully!";
                    $_SESSION['user_name'] = $data['name']; // Update session name
                } else {
                    $errors[] = "Failed to update profile";
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
            }
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        require __DIR__ . '/../../views/profile/edit.php';
    }
    
    public function changePassword(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            $errors = [];
            
            if (strlen($newPassword) < 8) {
                $errors[] = "New password must be at least 8 characters";
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = "New passwords do not match";
            }
            
            $user = $this->userModel->findById($_SESSION['user_id']);
            
            if (!password_verify($currentPassword, $user['password'])) {
                $errors[] = "Current password is incorrect";
            }
            
            if (empty($errors)) {
                if ($this->userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                    $_SESSION['success'] = "Password updated successfully!";
                    header('Location: /profile');
                    exit;
                } else {
                    $errors[] = "Failed to update password";
                }
            }
            
            $_SESSION['errors'] = $errors;
        }
        
        header('Location: /profile');
        exit;
    }
} 