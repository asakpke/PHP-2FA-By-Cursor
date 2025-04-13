<?php
declare(strict_types=1);

namespace App\Models;

class ActivityLog {
    private \PDO $db;
    
    public function __construct(\PDO $db) {
        $this->db = $db;
    }
    
    public function log(int $userId, string $action, array $details = []): bool {
        $sql = "INSERT INTO activity_logs (user_id, action, details, created_at) 
                VALUES (:user_id, :action, :details, :created_at)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'details' => json_encode($details),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getUserActivities(int $userId, int $limit = 10): array {
        $sql = "SELECT * FROM activity_logs 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private function getBrowserInfo(): string {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser = 'Unknown Browser';
        
        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        }
        
        return $browser;
    }
    
    private function getOSInfo(): string {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $os = 'Unknown OS';
        
        if (strpos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $os = 'MacOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        } elseif (strpos($userAgent, 'iPhone') !== false) {
            $os = 'iOS';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $os = 'Android';
        }
        
        return $os;
    }
    
    public function logLogin(int $userId): bool {
        return $this->log($userId, 'login', [
            'browser' => $this->getBrowserInfo(),
            'os' => $this->getOSInfo(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ]);
    }
} 