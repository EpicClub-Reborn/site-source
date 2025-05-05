<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Item;

class AvatarController {
    private User $userModel;
    private Item $itemModel;

    public function __construct() {
        $this->userModel = new User();
        $this->itemModel = new Item();
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

        // Fetch current customization
        $customization = $this->userModel->getCustomization($userId);
        if (!$customization) {
            $this->userModel->createCustomization($userId);
            $customization = $this->userModel->getCustomization($userId);
        }

        // Fetch items for each category
        $hats = $this->itemModel->getItemsByType('HAT', $userId);
        $shirts = $this->itemModel->getItemsByType('SHIRT', $userId);
        $pants = $this->itemModel->getItemsByType('PANTS', $userId);
        $faces = $this->itemModel->getItemsByType('FACE', $userId);
        $accessories = $this->itemModel->getItemsByType('ACCESSORY', $userId);
        $tools = $this->itemModel->getItemsByType('TOOL', $userId);
        $masks = $this->itemModel->getItemsByType('MASK', $userId);
        $eyes = $this->itemModel->getItemsByType('EYES', $userId);
        $hair = $this->itemModel->getItemsByType('HAIR', $userId);
        $heads = $this->itemModel->getItemsByType('HEAD', $userId);
        $others = $this->itemModel->getItemsByType('OTHER', $userId);

        // Handle save customization
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_customization') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $customizationData = [
                    'HAT_ID' => (int)($_POST['hat'] ?? 0),
                    'SHIRT_ID' => (int)($_POST['shirt'] ?? 0),
                    'PANTS_ID' => (int)($_POST['pants'] ?? 0),
                    'FACE_ID' => (int)($_POST['face'] ?? 0),
                    'ACCESSORY_ID' => (int)($_POST['accessory'] ?? 0),
                    'TOOL_ID' => (int)($_POST['tool'] ?? 0),
                    'MASK_ID' => (int)($_POST['mask'] ?? 0),
                    'EYES_ID' => (int)($_POST['eyes'] ?? 0),
                    'HAIR_ID' => (int)($_POST['hair'] ?? 0),
                    'HEAD_ID' => (int)($_POST['head'] ?? 0),
                    'OTHER_ID' => (int)($_POST['other'] ?? 0),
                ];

                if ($this->userModel->updateCustomization($userId, $customizationData)) {
                    $success = 'Avatar updated successfully!';
                    $customization = $this->userModel->getCustomization($userId);
                } else {
                    $error = 'Failed to update avatar.';
                }
            }
        }

        // Handle reset avatar
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_avatar') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                if ($this->userModel->resetAvatar($userId)) {
                    $success = 'Avatar reset successfully!';
                    $customization = $this->userModel->getCustomization($userId);
                    $user = $this->userModel->findByUsername($username);
                } else {
                    $error = 'Failed to reset avatar.';
                }
            }
        }

        $title = 'Customize Avatar - EpicClub';
        require BASE_PATH . '/app/Views/avatar/edit.php';
    }
}
?>