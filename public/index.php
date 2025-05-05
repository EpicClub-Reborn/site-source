<?php
session_start();
define('BASE_PATH', dirname(__DIR__));

$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('Autoloader not found at: ' . $autoloadPath);
}
require_once $autoloadPath;

$databasePath = BASE_PATH . '/config/database.php';
if (!file_exists($databasePath)) {
    die('Database config not found at: ' . $databasePath);
}
require_once $databasePath;

require_once BASE_PATH . '/app/Helpers/session.php';
require_once BASE_PATH . '/app/Helpers/csrf.php';

// Check maintenance mode and banned status
$userModel = new App\Models\User();
$settings = $userModel->getSiteSettings();
$maintenanceMode = $settings['maintenance_mode'] ?? 'off';
$isAdmin = false;
$isBanned = false;

if (isLoggedIn()) {
    $user = $userModel->findByUsername($_SESSION['username']);
    $isAdmin = $user && $user['POWER'] !== 'MEMBER' && $user['POWER'] !== 'MODERATOR';
    $isBanned = $user && $user['BANNED'] === 'YES';
}

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = rtrim($request_uri, '/');
$request_uri = strtolower($request_uri);

// Redirect banned users to /banned, unless accessing /banned or /logout
if ($isBanned && $request_uri !== '/banned' && $request_uri !== '/banned.php' && $request_uri !== '/logout' && $request_uri !== '/logout.php') {
    header("Location: /banned");
    exit;
}

// Redirect to maintenance page if maintenance mode is on, unless accessing /maintenance, /maintenance/login, admin routes, or if user is an admin
if ($maintenanceMode === 'on' && $request_uri !== '/maintenance' && $request_uri !== '/maintain.php' && $request_uri !== '/maintenance/login' && strpos($request_uri, '/admin') !== 0 && !$isAdmin) {
    header("Location: /maintenance");
    exit;
}

switch ($request_uri) {
    case '':
    case '/':
    case '/index.php':
    case '/epicclub-reborn':
    case '/epicclub-reborn/':
        $home = new App\Controllers\HomeController();
        $home->index();
        break;
    case '/login':
    case '/login.php':
        $auth = new App\Controllers\AuthController();
        $auth->login();
        break;
    case '/register':
    case '/register.php':
        $auth = new App\Controllers\AuthController();
        $auth->register();
        break;
    case '/logout':
    case '/logout.php':
        $auth = new App\Controllers\AuthController();
        $auth->logout();
        break;
    case '/dashboard':
    case '/dashboard/':
        $dashboard = new App\Controllers\DashboardController();
        $dashboard->index();
        break;
    case '/dashboard/update-status':
        $dashboard = new App\Controllers\DashboardController();
        $dashboard->updateStatus();
        break;
    case '/template':
    case '/template.php':
        $dashboard = new App\Controllers\DashboardController();
        $dashboard->template();
        break;
    case '/avatar/edit':
        $avatar = new App\Controllers\AvatarController();
        $avatar->edit();
        break;
    case '/avatar/upload':
        $avatar = new App\Controllers\AvatarController();
        $avatar->upload();
        break;
    case '/emporium':
        $emporium = new App\Controllers\EmporiumController();
        $emporium->index();
        break;
    case '/emporium/upload':
        $emporium = new App\Controllers\EmporiumController();
        $emporium->upload();
        break;
    case '/maintenance':
    case '/maintain.php':
        $maintenance = new App\Controllers\MaintenanceController();
        $maintenance->index();
        break;
    case '/maintenance/login':
        $maintenance = new App\Controllers\MaintenanceController();
        $maintenance->login();
        break;
    case '/profile/edit':
        $profile = new App\Controllers\ProfileController();
        $profile->edit();
        break;
    case '/banned':
    case '/banned.php':
        $banned = new App\Controllers\BannedController();
        $banned->index();
        break;
    case '/friends':
        $friends = new App\Controllers\FriendsController();
        $friends->index();
        break;
    case '/admin':
        $admin = new App\Controllers\AdminController();
        $admin->index();
        break;
    case '/admin/users':
        $admin = new App\Controllers\AdminController();
        $admin->users();
        break;
    case '/admin/creations':
        $admin = new App\Controllers\AdminController();
        $admin->creations();
        break;
    case '/admin/audit-logs':
        $admin = new App\Controllers\AdminController();
        $admin->auditLogs();
        break;
    case '/admin/announcements':
        $admin = new App\Controllers\AdminController();
        $admin->announcements();
        break;
    case '/admin/settings':
        $admin = new App\Controllers\AdminController();
        $admin->settings();
        break;
    default:
        // Handle /admin/users/view/[USER_ID]
        if (preg_match('#^/admin/users/view/(\d+)$#', $request_uri, $matches)) {
            $userId = $matches[1];
            $admin = new App\Controllers\AdminController();
            $admin->viewUser($userId);
            break;
        }
        // Handle /user/[USER_ID]
        if (preg_match('#^/user/(\d+)$#', $request_uri, $matches)) {
            $userId = $matches[1];
            $userController = new App\Controllers\UserController();
            $userController->view($userId);
            break;
        }
        http_response_code(404);
        echo 'Page not found';
        break;
}
?>