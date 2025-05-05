<?php
namespace App\Controllers;

use App\Models\User;

class DashboardController {
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

        $lastOnline = $user['LAST_ONLINE'];
        $currentTime = time();
        $timeSinceLastOnline = $currentTime - $lastOnline;
        $notification = '';

        if ($timeSinceLastOnline > 86400) {
            $this->userModel->updateLastOnline($userId, $currentTime);
            $dailyCoins = $user['DAILY_COINS'] + 1;
            $gold = $user['GOLD'] + 20;
            $silver = $user['SILVER'] + 5;

            if ($dailyCoins >= 30) {
                $gold += 500;
                $silver += 250;
                $dailyCoins = 0;
                $notification = "You have gained 500 Gold and 250 Silver for logging in 30 days in a row!";
            } else {
                $notification = "You have gained 20 Gold and 5 Silver for logging in today!";
            }

            $this->userModel->updateDailyBonus($userId, $dailyCoins, $gold, $silver);
            $user = $this->userModel->findByUsername($username);
        }

        // Fetch active announcements
        $announcements = $this->userModel->getActiveAnnouncements();

        // Fetch friends and their statuses
        $friends = $this->userModel->getFriends($userId, 10); // Limit to 10 friends for display
        $friendStatuses = [];
        foreach ($friends as $friend) {
            $friendData = $this->userModel->findById($friend['ID']);
            if ($friendData) {
                $friendStatuses[] = [
                    'USERNAME' => $friendData['USERNAME'],
                    'AVATAR_IMG_URL' => $friendData['AVATAR_IMG_URL'],
                    'STATUS' => $friendData['STATUS'],
                    'LAST_ONLINE' => $friendData['LAST_ONLINE']
                ];
            }
        }

        // Check for pending friend requests
        $pendingFriendRequests = $this->userModel->getPendingFriendRequests($userId);
        $pendingCount = count($pendingFriendRequests);
        $friendRequestNotification = $pendingCount > 0 ? "You have $pendingCount pending friend request(s)! <a href='/friends' class='text-blue-500 hover:underline'>View them</a>" : '';

        $title = 'Dashboard - EpicClub';
        require BASE_PATH . '/app/Views/dashboard/index.php';
    }

    public function updateStatus(): void {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid CSRF token.';
            } elseif (empty($_POST['status'])) {
                $_SESSION['error'] = 'Status cannot be empty.';
            } elseif (strlen($_POST['status']) > 50) {
                $_SESSION['error'] = 'Status must be 50 characters or less.';
            } else {
                $this->userModel->updateProfile($userId, $user['BIO'], $_POST['status'], $user['AVATAR_IMG_URL']);
                $_SESSION['success'] = 'Status updated successfully!';
            }
        }

        header("Location: /dashboard");
        exit;
    }

    public function template(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $title = 'Template - EpicClub';
        require BASE_PATH . '/app/Views/dashboard/template.php';
    }
}
?>