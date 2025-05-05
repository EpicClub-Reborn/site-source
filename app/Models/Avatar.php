<?php
namespace App\Models;

class Avatar {
    private \mysqli $db;

    public function __construct() {
        $this->db = \getDatabaseConnection();
    }

    public function saveAvatar(int $userId, string $filename): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET AVATAR_IMG_URL = ? WHERE ID = ?");
        $stmt->bind_param("si", $filename, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getUploadsCount(int $userId, string $status = 'ACCEPTED'): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ec_user_assets WHERE CREATOR_ID = ? AND STATUS = ?");
        $stmt->bind_param("is", $userId, $status);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'];
    }

    public function getUserAssets(int $userId, string $status = 'ACCEPTED'): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_user_assets WHERE CREATOR_ID = ? AND STATUS = ?");
        $stmt->bind_param("is", $userId, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $assets = [];
        while ($row = $result->fetch_assoc()) {
            $assets[] = $row;
        }
        $stmt->close();
        return $assets;
    }

    public function saveCharacter(int $userId, ?int $shirtId, ?int $trousersId): bool {
        $stmt = $this->db->prepare("UPDATE ec_users SET SHIRT_ID = ?, TROUSERS_ID = ? WHERE ID = ?");
        $stmt->bind_param("iii", $shirtId, $trousersId, $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>