<?php
namespace App\Controllers;

use App\Models\User;

class ProfileController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function edit(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            session_destroy();
            header("Location: /login");
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bio = trim($_POST['bio'] ?? '');
            $status = trim($_POST['status'] ?? '');
            $avatarUrl = trim($_POST['avatar_url'] ?? '');

            // Validate CSRF token
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            }
            // Validate fields
            elseif (empty($bio) || empty($status)) {
                $error = 'Bio and Status are required.';
            }
            elseif (strlen($bio) > 100) {
                $error = 'Bio must be 100 characters or less.';
            }
            elseif (strlen($status) > 50) {
                $error = 'Status must be 50 characters or less.';
            }
            elseif (!empty($avatarUrl) && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                $error = 'Avatar URL must be a valid URL.';
            }
            else {
                // Update profile
                $success = $this->userModel->updateProfile($userId, $bio, $status, $avatarUrl ?: $user['AVATAR_IMG_URL']);

                if ($success) {
                    $success = 'Profile updated successfully!';
                    $user = $this->userModel->findByUsername($username); // Refresh user data
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
            }
        }

        // Render edit profile view
        $title = 'Edit Profile - EpicClub';
        require BASE_PATH . '/app/Views/profile/edit.php';
    }
}
?>