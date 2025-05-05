<?php
namespace App\Controllers;

use App\Models\User;

class MaintenanceController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index(): void {
        $settings = $this->userModel->getSiteSettings();
        $maintenanceMode = $settings['maintenance_mode'] ?? 'off';

        if ($maintenanceMode === 'off') {
            header("Location: /");
            exit;
        }

        // Check if the user is already logged in as an admin
        if (isLoggedIn()) {
            $user = $this->userModel->findByUsername($_SESSION['username']);
            if ($user && $user['POWER'] !== 'MEMBER' && $user['POWER'] !== 'MODERATOR') {
                header("Location: /dashboard");
                exit;
            }
        }

        $title = 'Maintenance - EpicClub';
        require BASE_PATH . '/app/Views/maintenance/index.php';
    }

    public function login(): void {
        $settings = $this->userModel->getSiteSettings();
        $maintenanceMode = $settings['maintenance_mode'] ?? 'off';

        if ($maintenanceMode === 'off') {
            header("Location: /");
            exit;
        }

        // Check if the user is already logged in as an admin
        if (isLoggedIn()) {
            $user = $this->userModel->findByUsername($_SESSION['username']);
            if ($user && $user['POWER'] !== 'MEMBER' && $user['POWER'] !== 'MODERATOR') {
                header("Location: /dashboard");
                exit;
            }
        }

        $error = '';

        // Handle admin login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_login') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } elseif (empty($username) || empty($password)) {
                $error = 'All fields are required.';
            } else {
                $user = $this->userModel->findByUsername($username);
                if ($user && password_verify($password, $user['PASSWORD'])) {
                    if ($user['POWER'] !== 'MEMBER' && $user['POWER'] !== 'MODERATOR') {
                        $_SESSION['user_id'] = $user['ID'];
                        $_SESSION['username'] = $user['USERNAME'];
                        header("Location: /dashboard");
                        exit;
                    } else {
                        $error = 'Only admins can log in during maintenance mode.';
                    }
                } else {
                    $error = 'Invalid credentials.';
                }
            }
        }

        $title = 'Admin Login - Maintenance - EpicClub';
        require BASE_PATH . '/app/Views/maintenance/login.php';
    }
}
?>