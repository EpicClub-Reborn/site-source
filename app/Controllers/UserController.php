<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function view(string $userId): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $currentUserId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $currentUser = $this->userModel->findByUsername($username);

        if (!$currentUser) {
            session_destroy();
            header("Location: /login");
            exit;
        }

        $user = $this->userModel->findById((int)$userId);
        if (!$user) {
            header("Location: /dashboard");
            exit;
        }

        $error = '';
        $success = '';

        // Handle add friend request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_friend') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } elseif ($currentUserId === (int)$userId) {
                $error = 'You cannot send a friend request to yourself.';
            } elseif ($this->userModel->areFriends($currentUserId, (int)$userId)) {
                $error = 'You are already friends with this user.';
            } elseif ($this->userModel->hasPendingFriendRequest($currentUserId, (int)$userId)) {
                $error = 'A friend request is already pending.';
            } else {
                if ($this->userModel->sendFriendRequest($currentUserId, (int)$userId, time())) {
                    $success = 'Friend request sent successfully!';
                } else {
                    $error = 'Failed to send friend request.';
                }
            }
        }

        $isAdmin = $currentUser['POWER'] !== 'MEMBER' && $currentUser['POWER'] !== 'MODERATOR';
        $banReasons = $isAdmin ? $this->userModel->getActiveBans($user['ID']) : [];

        $title = "Profile - {$user['USERNAME']} - EpicClub";
        require BASE_PATH . '/app/Views/user/view.php';
    }
}
?>