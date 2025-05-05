<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Item;

class AdminController {
    private User $userModel;
    private Item $itemModel;

    public function __construct() {
        $this->userModel = new User();
        $this->itemModel = new Item();
    }

    public function index(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $error = '';
        $success = '';
        $isModerator = $user['POWER'] === 'MODERATOR';

        // Handle approve/disapprove items (moderators can approve/disapprove)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'approve_item' || $_POST['action'] === 'disapprove_item')) {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $itemId = (int)$_POST['item_id'];
                $action = $_POST['action'];
                $disapproveReason = trim($_POST['disapprove_reason'] ?? '');

                $item = $this->itemModel->getItemById($itemId);
                if ($item) {
                    if ($action === 'approve_item') {
                        $this->itemModel->updateItemStatus($itemId, 'ACCEPTED');
                        $success = "Item {$item['NAME']} has been approved.";
                        $this->userModel->logAdminAction('APPROVE_ITEM', $item['CREATOR_ID'], $user['ID'], "Approved item: {$item['NAME']}", time());
                    } elseif ($action === 'disapprove_item') {
                        if (empty($disapproveReason)) {
                            $error = 'Disapproval reason is required.';
                        } else {
                            $this->itemModel->updateItemStatus($itemId, 'REJECTED');
                            $this->userModel->sendMessage(
                                0,
                                $item['CREATOR_ID'],
                                "Your item '{$item['NAME']}' was disapproved. Reason: $disapproveReason",
                                time()
                            );
                            $success = "Item {$item['NAME']} has been disapproved, and the creator has been notified.";
                            $this->userModel->logAdminAction('DISAPPROVE_ITEM', $item['CREATOR_ID'], $user['ID'], "Disapproved item: {$item['NAME']}, Reason: $disapproveReason", time());
                        }
                    }
                } else {
                    $error = 'Item not found.';
                }
            }
        }

        // Handle bulk approve/disapprove items
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && ($_POST['action'] === 'bulk_approve' || $_POST['action'] === 'bulk_disapprove')) {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $itemIds = $_POST['item_ids'] ?? [];
                $disapproveReason = trim($_POST['bulk_disapprove_reason'] ?? '');

                if (empty($itemIds)) {
                    $error = 'No items selected.';
                } elseif ($_POST['action'] === 'bulk_disapprove' && empty($disapproveReason)) {
                    $error = 'Disapproval reason is required for bulk disapproval.';
                } else {
                    $action = $_POST['action'] === 'bulk_approve' ? 'ACCEPTED' : 'REJECTED';
                    $actionLogType = $_POST['action'] === 'bulk_approve' ? 'APPROVE_ITEM' : 'DISAPPROVE_ITEM';
                    $successCount = 0;

                    foreach ($itemIds as $itemId) {
                        $itemId = (int)$itemId;
                        $item = $this->itemModel->getItemById($itemId);
                        if ($item) {
                            $this->itemModel->updateItemStatus($itemId, $action);
                            if ($action === 'REJECTED') {
                                $this->userModel->sendMessage(
                                    0,
                                    $item['CREATOR_ID'],
                                    "Your item '{$item['NAME']}' was disapproved. Reason: $disapproveReason",
                                    time()
                                );
                                $this->userModel->logAdminAction($actionLogType, $item['CREATOR_ID'], $user['ID'], "Disapproved item: {$item['NAME']}, Reason: $disapproveReason", time());
                            } else {
                                $this->userModel->logAdminAction($actionLogType, $item['CREATOR_ID'], $user['ID'], "Approved item: {$item['NAME']}", time());
                            }
                            $successCount++;
                        }
                    }

                    $success = "$successCount item(s) have been " . ($_POST['action'] === 'bulk_approve' ? 'approved' : 'disapproved') . ".";
                }
            }
        }

        // Handle delete IP ban (moderators cannot delete IP bans)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_ip_ban') {
            if ($isModerator) {
                $error = 'Moderators are not allowed to delete IP bans.';
            } elseif (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $ipBanId = (int)$_POST['ip_ban_id'];
                if ($this->userModel->deleteIpBan($ipBanId)) {
                    $success = "IP ban has been removed.";
                    $this->userModel->logAdminAction('DELETE_IP_BAN', null, $user['ID'], "Deleted IP ban ID: $ipBanId", time());
                } else {
                    $error = "Failed to remove IP ban.";
                }
            }
        }

        $pendingItems = $this->itemModel->getPendingItems();
        $ipBans = $isModerator ? [] : $this->userModel->getIpBans(); // Moderators cannot view IP bans

        $title = 'Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/index.php';
    }

    public function users(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $isModerator = $user['POWER'] === 'MODERATOR';
        $users = $this->userModel->getAllUsers();

        $title = 'Manage Users - Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/users.php';
    }

    public function viewUser(string $userId): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $currentUserId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $currentUser = $this->userModel->findByUsername($username);

        if (!$currentUser || $currentUser['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $isModerator = $currentUser['POWER'] === 'MODERATOR';

        $user = $this->userModel->findById((int)$userId, true);
        if (!$user) {
            header("Location: /admin/users");
            exit;
        }

        $error = '';
        $success = '';

        // Handle ban user (moderators can only do temporary bans)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ban_user') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                $reasonType = trim($_POST['reason_type'] ?? '');
                $customReason = trim($_POST['custom_reason'] ?? '');
                $duration = $_POST['duration'];

                $reason = $reasonType === 'custom' ? $customReason : $reasonType;
                if (empty($reason)) {
                    $error = 'Ban reason is required.';
                } elseif ($duration <= 0 && $duration !== 'Permanent') {
                    $error = 'Invalid ban duration.';
                } elseif ($isModerator && $duration === 'Permanent') {
                    $error = 'Moderators cannot issue permanent bans.';
                } else {
                    $length = $duration === 'Permanent' ? -11122000 : (int)$duration;
                    $this->userModel->banUser($targetId, $currentUser['ID'], $reason, time(), $length);
                    $success = "User {$user['USERNAME']} has been banned.";
                    $user = $this->userModel->findById($targetId, true); // Refresh user data
                }
            }
        }

        // Handle warn user (moderators can warn users)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'warn_user') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                $warning = trim($_POST['warning'] ?? '');

                if (empty($warning)) {
                    $error = 'Warning message is required.';
                } else {
                    $this->userModel->addUserNote($targetId, $currentUser['ID'], 'WARNING', $warning, time());
                    $success = "Warning sent to {$user['USERNAME']}.";
                }
            }
        }

        // Handle add user note (moderators can add notes)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_note') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                $note = trim($_POST['note'] ?? '');

                if (empty($note)) {
                    $error = 'Note content is required.';
                } else {
                    $this->userModel->addUserNote($targetId, $currentUser['ID'], 'NOTE', $note, time());
                    $success = "Note added for {$user['USERNAME']}.";
                }
            }
        }

        // Handle IP ban (moderators cannot perform IP bans)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ip_ban') {
            if ($isModerator) {
                $error = 'Moderators are not allowed to perform IP bans.';
            } elseif (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                $reason = trim($_POST['ip_ban_reason'] ?? '');

                $targetUser = $this->userModel->findById($targetId, true);
                if ($targetUser) {
                    $ip = $targetUser['IP'];
                    if (empty($ip)) {
                        $error = 'User has no recorded IP address.';
                    } elseif (empty($reason)) {
                        $error = 'IP ban reason is required.';
                    } else {
                        $this->userModel->banIp($ip, $currentUser['ID'], $reason, time());
                        $success = "IP {$ip} has been banned.";
                    }
                } else {
                    $error = 'User not found.';
                }
            }
        }

        // Handle reset avatar (moderators can reset avatars)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_avatar') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                if ($this->userModel->resetAvatar($targetId)) {
                    $success = "User {$user['USERNAME']}'s avatar has been reset.";
                    $this->userModel->logAdminAction('RESET_AVATAR', $targetId, $currentUser['ID'], "Reset avatar for user: {$user['USERNAME']}", time());
                    $user = $this->userModel->findById($targetId, true); // Refresh user data
                } else {
                    $error = "Failed to reset avatar for {$user['USERNAME']}.";
                }
            }
        }

        // Handle unban user (moderators can unban users)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'unban_user') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                if ($this->userModel->reactivateAccount($targetId)) {
                    $success = "User {$user['USERNAME']} has been unbanned.";
                    $this->userModel->logAdminAction('UNBAN_USER', $targetId, $currentUser['ID'], "Unbanned user: {$user['USERNAME']}", time());
                    $user = $this->userModel->findById($targetId, true); // Refresh user data
                } else {
                    $error = "Failed to unban user {$user['USERNAME']}.";
                }
            }
        }

        // Handle change user role (moderators cannot change roles of ADMIN or MODERATOR users)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_role') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $targetId = (int)$_POST['user_id'];
                $newRole = trim($_POST['role'] ?? '');

                if (!in_array($newRole, ['MEMBER', 'MODERATOR', 'ADMIN'])) {
                    $error = 'Invalid role selected.';
                } elseif ($targetId === $currentUser['ID']) {
                    $error = 'You cannot change your own role.';
                } elseif ($isModerator && ($user['POWER'] === 'ADMIN' || $user['POWER'] === 'MODERATOR')) {
                    $error = 'Moderators cannot change roles of Admins or Moderators.';
                } else {
                    if ($this->userModel->updateUserRole($targetId, $newRole)) {
                        $success = "User {$user['USERNAME']}'s role has been updated to {$newRole}.";
                        $this->userModel->logAdminAction('CHANGE_ROLE', $targetId, $currentUser['ID'], "Changed role to $newRole for user: {$user['USERNAME']}", time());
                        $user = $this->userModel->findById($targetId, true); // Refresh user data
                    } else {
                        $error = "Failed to update user role.";
                    }
                }
            }
        }

        // Fetch ban reasons, notes, and recent activity
        $banReasons = $this->userModel->getActiveBans($user['ID']);
        $notes = $this->userModel->getUserNotes($user['ID']);
        $recentActivity = $this->userModel->getRecentActivity($user['ID']);

        $title = "User {$user['USERNAME']} - Admin Panel - EpicClub";
        require BASE_PATH . '/app/Views/admin/user_view.php';
    }

    public function creations(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        if ($user['POWER'] === 'MODERATOR') {
            header("Location: /admin");
            exit;
        }

        $error = '';
        $success = '';

        // Handle item creation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_item') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = (int)$_POST['price'];
                $type = trim($_POST['type'] ?? '');

                if (empty($name) || $price <= 0 || empty($type)) {
                    $error = 'All fields are required, and price must be greater than 0.';
                } elseif (!in_array($type, ['HAT', 'SHIRT', 'PANTS', 'FACE', 'ACCESSORY', 'TOOL', 'MASK', 'EYES', 'HAIR', 'HEAD', 'OTHER'])) {
                    $error = 'Invalid item type.';
                } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed.';
                } else {
                    $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
                    $fileType = mime_content_type($_FILES['image']['tmp_name']);
                    if (!in_array($fileType, $allowedTypes)) {
                        $error = 'Only PNG, JPEG, and GIF images are allowed.';
                    } else {
                        $uploadDir = BASE_PATH . '/public/assets/img/items/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
                        $uploadPath = $uploadDir . $fileName;
                        $imageUrl = '/assets/img/items/' . $fileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                            $createdAt = time();
                            if ($this->itemModel->createItem($name, $description, $user['ID'], $createdAt, $price, $imageUrl, $type, true)) {
                                $success = "Item {$name} has been created.";
                                $this->userModel->logAdminAction('CREATE_ITEM', null, $user['ID'], "Created item: $name", $createdAt);
                            } else {
                                $error = "Failed to create item.";
                                unlink($uploadPath); // Delete the uploaded file if creation fails
                            }
                        } else {
                            $error = "Failed to upload image.";
                        }
                    }
                }
            }
        }

        $title = 'Creations - Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/creations.php';
    }

    public function auditLogs(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $isModerator = $user['POWER'] === 'MODERATOR';
        $logs = $this->userModel->getAdminLogs();

        $title = 'Audit Logs - Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/audit_logs.php';
    }

    public function announcements(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $isModerator = $user['POWER'] === 'MODERATOR';
        $error = '';
        $success = '';

        // Handle create announcement
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_announcement') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $duration = (int)$_POST['duration'];

                if (empty($title) || empty($content) || $duration <= 0) {
                    $error = 'All fields are required, and duration must be greater than 0.';
                } else {
                    $createdAt = time();
                    $expiresAt = $createdAt + ($duration * 86400); // Convert days to seconds
                    if ($this->userModel->createAnnouncement($title, $content, $createdAt, $expiresAt, $user['ID'])) {
                        $success = "Announcement '$title' has been created.";
                    } else {
                        $error = "Failed to create announcement.";
                    }
                }
            }
        }

        // Handle delete announcement
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_announcement') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $announcementId = (int)$_POST['announcement_id'];
                if ($this->userModel->deleteAnnouncement($announcementId, $user['ID'])) {
                    $success = "Announcement has been deleted.";
                } else {
                    $error = "Failed to delete announcement.";
                }
            }
        }

        $announcements = $this->userModel->getAllAnnouncements();

        $title = 'Announcements - Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/announcements.php';
    }

    public function settings(): void {
        if (!isLoggedIn()) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $user = $this->userModel->findByUsername($username);

        if (!$user || $user['POWER'] === 'MEMBER') {
            header("Location: /dashboard");
            exit;
        }

        $isModerator = $user['POWER'] === 'MODERATOR'; // Define $isModerator
        $error = '';
        $success = '';

        // Handle update settings
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token.';
            } else {
                $settings = $_POST['settings'] ?? [];
                foreach ($settings as $key => $value) {
                    $this->userModel->updateSiteSetting($key, $value);
                }
                $success = "Settings have been updated.";
                $this->userModel->logAdminAction('UPDATE_SETTINGS', null, $user['ID'], "Updated site settings", time());
            }
        }

        $settings = $this->userModel->getSiteSettings();

        $title = 'Settings - Admin Panel - EpicClub';
        require BASE_PATH . '/app/Views/admin/settings.php';
    }
}
?>