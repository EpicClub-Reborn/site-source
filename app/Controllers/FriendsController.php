<?php
namespace App\Controllers;

use App\Models\User;

class FriendsController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index(): void {
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

        // Handle accept/reject friend request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $requestId = (int)$_POST['request_id'];
                if ($_POST['action'] === 'accept_friend') {
                    if ($this->userModel->acceptFriendRequest($requestId, $userId)) {
                        $success = 'Friend request accepted.';
                    } else {
                        $error = 'Failed to accept friend request.';
                    }
                } elseif ($_POST['action'] === 'reject_friend') {
                    if ($this->userModel->rejectFriendRequest($requestId, $userId)) {
                        $success = 'Friend request rejected.';
                    } else {
                        $error = 'Failed to reject friend request.';
                    }
                }
            }
        }

        // Determine which tab to display
        $tab = $_GET['tab'] ?? 'friends'; // Default to 'friends' if no tab is specified
        if (!in_array($tab, ['pending', 'friends'])) {
            $tab = 'friends'; // Fallback to 'friends' if invalid tab
        }

        $friends = $this->userModel->getFriends($userId, 50); // Limit to 50 friends for display
        $pendingRequests = $this->userModel->getPendingFriendRequests($userId);
        $pendingCount = count($pendingRequests);

        $title = 'Friends - EpicClub';
        require BASE_PATH . '/app/Views/friends/index.php';
    }
}
?>