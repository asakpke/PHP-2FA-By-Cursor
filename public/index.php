<?php
declare(strict_types=1);

// Set default timezone
date_default_timezone_set('Asia/Kolkata'); // Change this to your timezone

// Error handling setup
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Custom error handler to log errors and handle critical ones
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error $errno: $errstr in $errfile on line $errline");
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        die("A critical error occurred. Please check the error logs.");
    }
});

try {
    session_start();

    require_once __DIR__ . '/../vendor/autoload.php';

    // Load environment variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    // Database connection
    $dbConfig = require __DIR__ . '/../config/database.php';
    try {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $dbConfig['host'],
            $dbConfig['dbname'],
            $dbConfig['charset']
        );
        
        $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Return associative arrays by default
        ]);
    } catch (PDOException $e) {
        throw new Exception('Database connection failed: ' . $e->getMessage());
    }

    // Initialize models and services
    try {
        $userModel = new App\Models\User($db);
        $emailVerification = new App\Models\EmailVerification($db);
        $activityLog = new App\Models\ActivityLog($db);
        $roleModel = new App\Models\Role($db);
        $twoFactorAuth = new App\Services\TwoFactorAuth();
        $apiAuth = new App\Services\ApiAuth($db);
    } catch (Exception $e) {
        throw new Exception('Service initialization failed: ' . $e->getMessage());
    }

    // Initialize controllers
    $authController = new App\Controllers\AuthController($userModel, $twoFactorAuth, $activityLog);
    $profileController = new App\Controllers\ProfileController($userModel);
    $passwordController = new App\Controllers\PasswordController($userModel);
    $apiController = new App\Controllers\ApiController($apiAuth);

    // Simple routing
    $route = $_SERVER['REQUEST_URI'];
    $route = strtok($route, '?');

    switch ($route) {
        case '/':
            require __DIR__ . '/../views/home.php';
            break;
        case '/register':
            $authController->register();
            break;
        case '/login':
            $authController->login();
            break;
        case '/logout':
            $authController->logout();
            break;
        case '/dashboard':
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }
            require __DIR__ . '/../views/dashboard.php';
            break;
        case '/profile':
            $profileController->edit();
            break;
        case '/profile/password':
            $profileController->changePassword();
            break;
        case '/forgot-password':
            $passwordController->forgot();
            break;
        case '/reset-password':
            $passwordController->reset();
            break;
        case '/verify-email':
            $authController->verifyEmail();
            break;
        case '/2fa/setup':
            $authController->setup2FA();
            break;
        case '/2fa/verify':
            $authController->verify2FA();
            break;
        case '/2fa/manage':
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }
            $authController->manage2FA();
            break;
        case '/2fa/disable':
            $authController->disable2FA();
            break;
        case '/api-keys':
            $apiController->manageKeys();
            break;
        default:
            http_response_code(404);
            echo '404 Not Found';
    }

    // After error reporting setup
    error_log("Server time: " . date('Y-m-d H:i:s'));
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = $_ENV['APP_ENV'] === 'development' ? $e->getMessage() : 'An error occurred.';
    require __DIR__ . '/../views/error.php';
} 