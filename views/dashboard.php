<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Get user's recent activities
$activities = $activityLog->getUserActivities($_SESSION['user_id'], 5);

$title = 'Dashboard';
$content = '
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Profile</div>
                <div class="card-body">
                    <h5 class="card-title">Welcome, ' . htmlspecialchars($_SESSION['user_name']) . '!</h5>
                    <p class="card-text">Manage your account settings and preferences.</p>
                    <a href="/profile" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">Security</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-shield-lock"></i> Two-Factor Authentication
                            </div>
                            <a href="/2fa/manage" class="btn btn-sm btn-outline-primary">Manage</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-key"></i> API Keys
                            </div>
                            <a href="/api-keys" class="btn btn-sm btn-outline-primary">Manage</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Recent Activity</div>
                <div class="card-body">
                    <div class="list-group">
                        ' . (empty($activities) ? '
                        <div class="text-center py-4">
                            <p class="text-muted">No recent activity</p>
                        </div>
                        ' : implode('', array_map(function($activity) {
                            $details = json_decode($activity['details'], true);
                            $timeAgo = getTimeAgo(strtotime($activity['created_at']));
                            
                            return '
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">' . ucfirst($activity['action']) . '</h6>
                                    <small class="text-muted" title="' . date('M j, Y g:i A', strtotime($activity['created_at'])) . '">' 
                                        . $timeAgo . '</small>
                                </div>
                                <p class="mb-1">
                                    ' . ($activity['action'] === 'login' ? 
                                    'You logged in from ' . $details['browser'] . ' on ' . $details['os'] :
                                    htmlspecialchars($activity['action'])) . '
                                </p>
                                <small class="text-muted">IP: ' . ($details['ip'] ?? 'Unknown') . '</small>
                            </div>';
                        }, $activities))) . '
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';

// Helper function for time ago format
function getTimeAgo($timestamp) {
    $currentTime = time();
    $difference = $currentTime - $timestamp;
    
    if ($difference < 60) {
        return 'Just now';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 604800) { // 7 days
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y g:i A', $timestamp);
    }
}

require __DIR__ . '/layouts/main.php'; 