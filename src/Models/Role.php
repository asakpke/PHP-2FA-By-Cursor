<?php
declare(strict_types=1);

namespace App\Models;

class Role {
    private \PDO $db;
    
    public function __construct(\PDO $db) {
        $this->db = $db;
    }
    
    public function hasPermission(int $userId, string $permission): bool {
        $sql = "SELECT p.name FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role_id
                WHERE ur.user_id = :user_id AND p.name = :permission";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'permission' => $permission]);
        return (bool) $stmt->fetch();
    }
} 