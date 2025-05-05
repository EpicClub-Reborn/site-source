<?php
namespace App\Models;

class User {
    private \mysqli $db;

    public function __construct() {
        $this->db = \getDatabaseConnection();
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT ID, USERNAME, PASSWORD, EMAIL, GENDER, GOLD, SILVER, POWER, JOINED, LAST_ONLINE, DAILY_COINS, BIO, AVATAR_IMG_URL, STATUS, VIP, BANNED, IP FROM ec_users WHERE USERNAME = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function findById(int $userId, bool $isAdmin = false): ?array {
        $stmt = $this->db->prepare("SELECT ID, USERNAME, PASSWORD, EMAIL, GENDER, GOLD, SILVER, POWER, JOINED, LAST_ONLINE, DAILY_COINS, BIO, AVATAR_IMG_URL, STATUS, VIP, BANNED, IP FROM ec_users WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && !$isAdmin) {
            $isPermanentlyBanned = $this->isPermanentlyBanned($userId);
            if ($isPermanentlyBanned) {
                return null; // Non-admins cannot view permanently banned users
            }
        }

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT ID, USERNAME, PASSWORD, EMAIL, GENDER, GOLD, SILVER, POWER, JOINED, LAST_ONLINE, DAILY_COINS, BIO, AVATAR_IMG_URL, STATUS, VIP, BANNED, IP FROM ec_users WHERE EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function updatePassword(int $userId, string $newHash): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET PASSWORD = ? WHERE ID = ?");
        $stmt->bind_param("si", $newHash, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function createUser(string $username, string $password, string $email, string $gender, int $time, string $uniString): bool {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->db->prepare(
            "INSERT INTO ec_users (USERNAME, PASSWORD, EMAIL, GENDER, GOLD, SILVER, POWER, VIP, BANNED, BANNED_TILL, JOINED, FORUM_POSTS, FORUM_SIG, LAST_ONLINE, IP, VERIFIED, BIO, STATUS, UNI_STRING, DAILY_COINS)
            VALUES (?, ?, ?, ?, 0, 20, 'MEMBER', 'NONE', 'NO', 0, ?, 0, '', ?, ?, 'NO', 'Just joined this site!', 'Hi, I am new here!', ?, 0)"
        );
        $stmt->bind_param("ssssisss", $username, $password, $email, $gender, $time, $time, $ip, $uniString);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            $userId = $this->db->insert_id;
            $this->createCustomization($userId);
        }

        return $success;
    }

    public function updateLastOnline(int $userId, int $time): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET LAST_ONLINE = ? WHERE ID = ?");
        $stmt->bind_param("ii", $time, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateDailyBonus(int $userId, int $dailyCoins, int $gold, int $silver): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET DAILY_COINS = ?, GOLD = ?, SILVER = ? WHERE ID = ?");
        $stmt->bind_param("iiii", $dailyCoins, $gold, $silver, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateProfile(int $userId, string $bio, string $status, string $avatarUrl): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET BIO = ?, STATUS = ?, AVATAR_IMG_URL = ? WHERE ID = ?");
        $stmt->bind_param("sssi", $bio, $status, $avatarUrl, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateAvatarUrl(int $userId, string $avatarUrl): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET AVATAR_IMG_URL = ? WHERE ID = ?");
        $stmt->bind_param("si", $avatarUrl, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function resetAvatar(int $userId): bool {
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }

        $templatePath = BASE_PATH . '/public/assets/img/template.png';
        $userAvatarPath = BASE_PATH . '/public/assets/avatars/avatar_' . $userId . '.png';
        $userAvatarUrl = '/assets/avatars/avatar_' . $userId . '.png';

        if (!file_exists($templatePath)) {
            error_log("template.png not found in public/assets/img/");
            return false;
        }

        if (copy($templatePath, $userAvatarPath)) {
            return $this->updateAvatarUrl($userId, $userAvatarUrl);
        } else {
            error_log("Failed to copy template.png to $userAvatarPath for user ID $userId");
            return false;
        }
    }

    public function hasUnreadMessages(int $userId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_messages WHERE RECEIVE_ID = ? AND SEEN = 'NO'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }

    public function countPendingAssets(): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_items WHERE STATUS = 'PENDING'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }

    public function countFriends(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_friends WHERE USER_ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }

    public function countUsersByIp(string $ip): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_users WHERE IP = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }

    public function isIpBanned(string $ip): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_ip_bans WHERE IP = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }

    public function getFriends(int $userId, int $limit): array {
        $stmt = $this->db->prepare(
            "SELECT u.ID, u.USERNAME, u.AVATAR_IMG_URL 
             FROM ec_users u 
             JOIN ec_friends f ON u.ID = f.FRIEND_ID 
             WHERE f.USER_ID = ? 
             LIMIT ?"
        );
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $friends = [];
        while ($row = $result->fetch_assoc()) {
            $friends[] = $row;
        }
        $stmt->close();
        return $friends;
    }

    public function getActiveBans(int $userId): array {
        $currentTime = time();
        $stmt = $this->db->prepare(
            "SELECT * FROM ec_ban_logs 
             WHERE USER_ID = ? AND (START_TIME + LENGTH > ? OR LENGTH = -11122000)"
        );
        $stmt->bind_param("ii", $userId, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $bans = [];
        while ($row = $result->fetch_assoc()) {
            $bans[] = $row;
        }
        $stmt->close();
        return $bans;
    }

    public function getTerminatedBan(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM ec_ban_logs WHERE USER_ID = ? AND LENGTH = '-11122000'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $ban = $result->fetch_assoc();
        $stmt->close();
        return $ban ?: null;
    }

    public function isPermanentlyBanned(int $userId): bool {
        return $this->getTerminatedBan($userId) !== null;
    }

    public function reactivateAccount(int $userId): bool {
        $this->db->begin_transaction();
        try {
            $stmt1 = $this->db->prepare("UPDATE ec_users SET BANNED = 'NO' WHERE ID = ?");
            $stmt1->bind_param("i", $userId);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare("DELETE FROM ec_ban_logs WHERE USER_ID = ?");
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();
            $stmt2->close();

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getCustomization(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM ec_user_customizations WHERE USER_ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $customization = $result->fetch_assoc();
        $stmt->close();
        return $customization;
    }

    public function createCustomization(int $userId): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_user_customizations (USER_ID) VALUES (?)");
        $stmt->bind_param("i", $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateCustomization(int $userId, array $customization): bool {
        $stmt = $this->db->prepare(
            "UPDATE ec_user_customizations 
             SET HAT_ID = ?, FACE_ID = ?, ACCESSORY_ID = ?, SHIRT_ID = ?, PANTS_ID = ? 
             WHERE USER_ID = ?"
        );
        $stmt->bind_param(
            "iiiiii",
            $customization['HAT_ID'],
            $customization['FACE_ID'],
            $customization['ACCESSORY_ID'],
            $customization['SHIRT_ID'],
            $customization['PANTS_ID'],
            $userId
        );
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAllUsers(): array {
        $stmt = $this->db->prepare("SELECT ID, USERNAME, JOINED, AVATAR_IMG_URL, BIO, STATUS, IP FROM ec_users ORDER BY JOINED DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
        return $users;
    }

    public function banUser(int $userId, int $modId, string $reason, int $startTime, int $length): bool {
        $this->db->begin_transaction();
        try {
            $stmt1 = $this->db->prepare("INSERT INTO ec_ban_logs (USER_ID, MOD_ID, REASON, START_TIME, LENGTH) VALUES (?, ?, ?, ?, ?)");
            $stmt1->bind_param("iisii", $userId, $modId, $reason, $startTime, $length);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare("UPDATE ec_users SET BANNED = 'YES' WHERE ID = ?");
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();
            $stmt2->close();

            if ($length === -11122000) {
                $randomId = mt_rand(10000, 99999);
                $newUsername = "Deleted_User_$randomId";
                $stmt3 = $this->db->prepare("UPDATE ec_users SET USERNAME = ?, BIO = 'This user has been permanently banned.', STATUS = 'Account Deleted' WHERE ID = ?");
                $stmt3->bind_param("si", $newUsername, $userId);
                $stmt3->execute();
                $stmt3->close();
            }

            // Log the action
            $this->logAdminAction('BAN_USER', $userId, $modId, "Banned user with reason: $reason, duration: " . ($length === -11122000 ? 'Permanent' : ($length / 86400) . ' days'), time());

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function addUserNote(int $userId, int $modId, string $noteType, string $content, int $createdAt): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_user_notes (USER_ID, MOD_ID, NOTE_TYPE, CONTENT, CREATED_AT) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $userId, $modId, $noteType, $content, $createdAt);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action
            $this->logAdminAction('ADD_NOTE', $userId, $modId, "Added $noteType: $content", $createdAt);
        }

        return $success;
    }

    public function getUserNotes(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_user_notes WHERE USER_ID = ? ORDER BY CREATED_AT DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        $stmt->close();
        return $notes;
    }

    public function banIp(string $ip, int $modId, string $reason, int $createdAt): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_ip_bans (IP, MOD_ID, REASON, CREATED_AT) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $ip, $modId, $reason, $createdAt);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action
            $this->logAdminAction('BAN_IP', null, $modId, "Banned IP $ip with reason: $reason", $createdAt);
        }

        return $success;
    }

    public function getIpBans(): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_ip_bans ORDER BY CREATED_AT DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $ipBans = [];
        while ($row = $result->fetch_assoc()) {
            $ipBans[] = $row;
        }
        $stmt->close();
        return $ipBans;
    }

    public function deleteIpBan(int $ipBanId): bool {
        $stmt = $this->db->prepare("DELETE FROM ec_ip_bans WHERE ID = ?");
        $stmt->bind_param("i", $ipBanId);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action (assuming $modId is available in the calling context, we'll handle this in the controller)
        }

        return $success;
    }

    public function getRecentActivity(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_recent_events WHERE USER_ID = ? ORDER BY CREATED_AT DESC LIMIT 10");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $activity = [];
        while ($row = $result->fetch_assoc()) {
            $activity[] = $row;
        }
        $stmt->close();
        return $activity;
    }

    public function sendMessage(int $senderId, int $receiverId, string $content, int $createdAt): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_messages (SENDER_ID, RECEIVE_ID, CONTENT, CREATED_AT, SEEN) VALUES (?, ?, ?, ?, 'NO')");
        $stmt->bind_param("iisi", $senderId, $receiverId, $content, $createdAt);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getSiteSettings(): array {
        $stmt = $this->db->prepare("SELECT * FROM site_settings");
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['SETTING_KEY']] = $row['SETTING_VALUE'];
        }
        $stmt->close();
        return $settings;
    }

    public function updateSiteSetting(string $key, string $value): bool {
        $stmt = $this->db->prepare("INSERT INTO site_settings (SETTING_KEY, SETTING_VALUE) VALUES (?, ?) ON DUPLICATE KEY UPDATE SETTING_VALUE = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateUserRole(int $userId, string $role): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET POWER = ? WHERE ID = ?");
        $stmt->bind_param("si", $role, $userId);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action (assuming $modId is available in the calling context, we'll handle this in the controller)
        }

        return $success;
    }

    public function logAdminAction(string $actionType, ?int $userId, int $modId, string $details, int $createdAt): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_admin_logs (ACTION_TYPE, USER_ID, MOD_ID, DETAILS, CREATED_AT) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siisi", $actionType, $userId, $modId, $details, $createdAt);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAdminLogs(): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_admin_logs ORDER BY CREATED_AT DESC LIMIT 100");
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        $stmt->close();
        return $logs;
    }

    public function createAnnouncement(string $title, string $content, int $createdAt, int $expiresAt, int $createdBy): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_announcements (TITLE, CONTENT, CREATED_AT, EXPIRES_AT, CREATED_BY) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $title, $content, $createdAt, $expiresAt, $createdBy);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action
            $this->logAdminAction('CREATE_ANNOUNCEMENT', null, $createdBy, "Created announcement: $title", $createdAt);
        }

        return $success;
    }

    public function getActiveAnnouncements(): array {
        $currentTime = time();
        $stmt = $this->db->prepare("SELECT * FROM ec_announcements WHERE EXPIRES_AT > ? ORDER BY CREATED_AT DESC");
        $stmt->bind_param("i", $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }
        $stmt->close();
        return $announcements;
    }

    public function getAllAnnouncements(): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_announcements ORDER BY CREATED_AT DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $announcements = [];
        while ($row = $result->fetch_assoc()) {
            $announcements[] = $row;
        }
        $stmt->close();
        return $announcements;
    }

    public function deleteAnnouncement(int $announcementId, int $modId): bool {
        $stmt = $this->db->prepare("DELETE FROM ec_announcements WHERE ID = ?");
        $stmt->bind_param("i", $announcementId);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            // Log the action
            $this->logAdminAction('DELETE_ANNOUNCEMENT', null, $modId, "Deleted announcement ID: $announcementId", time());
        }

        return $success;
    }

    public function sendFriendRequest(int $senderId, int $receiverId, int $createdAt): bool {
        $stmt = $this->db->prepare("INSERT INTO ec_friend_requests (SENDER_ID, RECEIVER_ID, CREATED_AT, STATUS) VALUES (?, ?, ?, 'PENDING')");
        $stmt->bind_param("iii", $senderId, $receiverId, $createdAt);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getPendingFriendRequests(int $userId): array {
        $stmt = $this->db->prepare("SELECT fr.ID, fr.SENDER_ID, fr.CREATED_AT, u.USERNAME, u.AVATAR_IMG_URL 
                                    FROM ec_friend_requests fr 
                                    JOIN ec_users u ON fr.SENDER_ID = u.ID 
                                    WHERE fr.RECEIVER_ID = ? AND fr.STATUS = 'PENDING'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        $stmt->close();
        return $requests;
    }

    public function countPendingFriendRequests(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_friend_requests WHERE RECEIVER_ID = ? AND STATUS = 'PENDING'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }

    public function acceptFriendRequest(int $requestId, int $userId): bool {
        $this->db->begin_transaction();
        try {
            // Verify the request exists and belongs to the user
            $stmt = $this->db->prepare("SELECT SENDER_ID, RECEIVER_ID FROM ec_friend_requests WHERE ID = ? AND RECEIVER_ID = ? AND STATUS = 'PENDING'");
            $stmt->bind_param("ii", $requestId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            $stmt->close();

            if (!$request) {
                $this->db->rollback();
                return false;
            }

            // Update the request status to ACCEPTED
            $stmt = $this->db->prepare("UPDATE ec_friend_requests SET STATUS = 'ACCEPTED' WHERE ID = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $stmt->close();

            // Add the friendship to ec_friends (bidirectional)
            $stmt = $this->db->prepare("INSERT INTO ec_friends (USER_ID, FRIEND_ID) VALUES (?, ?)");
            $stmt->bind_param("ii", $request['RECEIVER_ID'], $request['SENDER_ID']);
            $stmt->execute();
            $stmt->bind_param("ii", $request['SENDER_ID'], $request['RECEIVER_ID']);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function rejectFriendRequest(int $requestId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE ec_friend_requests SET STATUS = 'REJECTED' WHERE ID = ? AND RECEIVER_ID = ? AND STATUS = 'PENDING'");
        $stmt->bind_param("ii", $requestId, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function hasPendingFriendRequest(int $senderId, int $receiverId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_friend_requests WHERE SENDER_ID = ? AND RECEIVER_ID = ? AND STATUS = 'PENDING'");
        $stmt->bind_param("ii", $senderId, $receiverId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }

    public function areFriends(int $userId1, int $userId2): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_friends WHERE USER_ID = ? AND FRIEND_ID = ?");
        $stmt->bind_param("ii", $userId1, $userId2);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }
}
?>