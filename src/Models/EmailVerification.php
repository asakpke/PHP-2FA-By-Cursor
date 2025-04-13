<?php
declare(strict_types=1);

namespace App\Models;

class EmailVerification {
    private \PDO $db;
    
    public function __construct(\PDO $db) {
        $this->db = $db;
    }
    
    public function createVerification(int $userId): string {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO email_verifications (user_id, token, created_at) VALUES (:user_id, :token, :created_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return $token;
    }
} 