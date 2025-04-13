<?php
declare(strict_types=1);

namespace App\Models;

use PDOException;

class User {
    private \PDO $db;
    
    public function __construct(\PDO $db) {
        $this->db = $db;
    }
    
    public function create(array $data): bool {
        $sql = "INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, :created_at)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function updateProfile(int $userId, array $data): bool {
        $sql = "UPDATE users SET 
                name = :name,
                email = :email,
                bio = :bio,
                updated_at = :updated_at
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'bio' => $data['bio'] ?? null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function updatePassword(int $userId, string $password): bool {
        $sql = "UPDATE users SET 
                password = :password,
                updated_at = :updated_at
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $userId,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function enable2FA(int $userId, string $secret): bool {
        $sql = "UPDATE users SET 
                two_factor_secret = :secret,
                two_factor_enabled = 1,
                updated_at = :updated_at
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $userId,
            'secret' => $secret,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function disable2FA(int $userId): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET 
                two_factor_secret = NULL,
                two_factor_enabled = 0,
                updated_at = :updated_at
                WHERE id = :id"
            );
            
            return $stmt->execute([
                'id' => $userId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log('Failed to disable 2FA: ' . $e->getMessage());
            return false;
        }
    }
} 