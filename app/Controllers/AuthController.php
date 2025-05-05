<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login(): void {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } elseif (empty($username) || empty($password)) {
                $error = 'All fields are required.';
            } else {
                $user = $this->userModel->findByUsername($username);
                if ($user && password_verify($password, $user['PASSWORD'])) {
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['username'] = $user['USERNAME'];
                    header("Location: /dashboard");
                    exit;
                } else {
                    $error = 'Invalid credentials.';
                }
            }
        }

        $title = 'Login - EpicClub';
        require BASE_PATH . '/app/Views/auth/login.php';
    }

    public function register(): void {
        $error = '';
        $username = '';
        $email = '';
        $gender = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $passwordConfirm = trim($_POST['password_confirm'] ?? '');
            $gender = trim($_POST['gender'] ?? '');

            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } elseif (empty($username) || empty($email) || empty($password) || empty($passwordConfirm) || empty($gender)) {
                $error = 'All fields are required.';
            } elseif (!preg_match('/^[a-zA-Z0-9]{4,20}$/', $username)) {
                if (strlen($username) < 4 || strlen($username) > 20) {
                    $error = 'Username must be between 4 and 20 characters.';
                } else {
                    $error = 'Invalid username syntax. Use only letters and numbers.';
                }
            } elseif ($this->userModel->findByUsername($username)) {
                $error = 'Username is already taken.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format.';
            } elseif ($this->userModel->findByEmail($email)) {
                $error = 'Email is already registered.';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Passwords do not match.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif (!in_array($gender, ['MALE', 'FEMALE', 'OTHER'])) {
                $error = 'Invalid gender selection.';
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
                if ($this->userModel->isIpBanned($ip)) {
                    $error = 'Your IP address is banned from creating accounts.';
                } else {
                    $ipCount = $this->userModel->countUsersByIp($ip);
                    if ($ipCount >= 3) {
                        $error = 'You already have 3 accounts.';
                    } else {
                        $time = time();
                        $uniString = bin2hex(random_bytes(16));
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        $success = $this->userModel->createUser($username, $hashedPassword, $email, $gender, $time, $uniString);
                        if ($success) {
                            $user = $this->userModel->findByUsername($username);
                            if ($user) {
                                // Ensure the avatars directory exists
                                $avatarsDir = BASE_PATH . '/public/assets/avatars/';
                                if (!is_dir($avatarsDir)) {
                                    mkdir($avatarsDir, 0755, true);
                                }

                                // Copy template.png to avatars/avatar_[USER_ID].png
                                $templatePath = BASE_PATH . '/public/assets/img/template.png';
                                $userAvatarPath = BASE_PATH . '/public/assets/avatars/avatar_' . $user['ID'] . '.png';
                                $userAvatarUrl = '/assets/avatars/avatar_' . $user['ID'] . '.png';

                                if (file_exists($templatePath)) {
                                    if (copy($templatePath, $userAvatarPath)) {
                                        // Update the user's AVATAR_IMG_URL
                                        $this->userModel->updateAvatarUrl($user['ID'], $userAvatarUrl);
                                    } else {
                                        error_log("Failed to copy template.png to $userAvatarPath for user ID {$user['ID']}");
                                    }
                                } else {
                                    error_log("template.png not found in public/assets/img/");
                                }

                                $_SESSION['user_id'] = $user['ID'];
                                $_SESSION['username'] = $user['USERNAME'];
                                header("Location: /dashboard");
                                exit;
                            }
                        }
                        $error = 'Registration failed. Please try again.';
                    }
                }
            }
        }

        $title = 'Register - EpicClub';
        require BASE_PATH . '/app/Views/auth/register.php';
    }

    public function logout(): void {
        session_destroy();
        header("Location: /");
        exit;
    }
}
?>