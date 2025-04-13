<?php
declare(strict_types=1);

namespace App\Controllers;

class ApiController {
    private $apiAuth;
    
    public function __construct($apiAuth) {
        $this->apiAuth = $apiAuth;
    }
    
    public function manageKeys(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $newApiKey = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'generate') {
                $apiKey = $this->apiAuth->generateApiKey($_SESSION['user_id']);
                $_SESSION['success'] = "New API key generated successfully!";
                $newApiKey = $apiKey; // Store the full key temporarily
            } elseif (isset($_POST['action']) && $_POST['action'] === 'revoke') {
                $keyId = filter_input(INPUT_POST, 'key_id', FILTER_SANITIZE_NUMBER_INT);
                if ($this->apiAuth->revokeApiKey($keyId)) {
                    $_SESSION['success'] = "API key revoked successfully!";
                }
            }
        }
        
        $apiKeys = $this->apiAuth->getUserApiKeys($_SESSION['user_id']);
        require __DIR__ . '/../../views/api/manage-keys.php';
    }
} 