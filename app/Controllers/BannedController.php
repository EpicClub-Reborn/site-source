<?php
namespace App\Controllers;

use App\Models\User;

class BannedController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $user = $this->userModel->findByUsername($_SESSION['username']);
        if (!$user || $user['BANNED'] !== 'YES') {
            header("Location: /dashboard");
            exit;
        }

        // Fetch the ban reason from ec_ban_logs
        $currentTime = time();
        $stmt = $this->userModel->getActiveBans($user['ID']);
        $banReason = '';
        $banStartTime = '';
        $banDuration = '';

        if (!empty($stmt)) {
            $ban = $stmt[0]; // Get the first active ban
            $banReason = $ban['REASON'];
            $banStartTime = date('Y-m-d H:i:s', $ban['START_TIME']);
            $banDuration = $ban['LENGTH'] === -11122000 ? 'Permanent' : ($ban['LENGTH'] / 86400) . ' days';
        }

        $title = 'Banned - EpicClub';
        require BASE_PATH . '/app/Views/banned/index.php';
    }
}
?>