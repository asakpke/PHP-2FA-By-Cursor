<?php
declare(strict_types=1);

namespace App\Services;

class ApiAuth {
    private \PDO $db;
    
    public function __construct(\PDO $db) {
        $this->db = $db;
    }
    
    public function generateApiKey(int $userId): string {
        $apiKey = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO api_keys (user_id, api_key, created_at) VALUES (:user_id, :api_key, :created_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'api_key' => $apiKey,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $apiKey;
    }
    
    public function getUserApiKeys(int $userId): array {
        $sql = "SELECT * FROM api_keys WHERE user_id = :user_id AND revoked_at IS NULL ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function revokeApiKey(int $keyId): bool {
        $sql = "UPDATE api_keys SET revoked_at = :revoked_at WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $keyId,
            'revoked_at' => date('Y-m-d H:i:s')
        ]);
    }
} 