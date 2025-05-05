<?php
namespace App\Models;

class Item {
    private \mysqli $db;

    public function __construct() {
        $this->db = \getDatabaseConnection();
    }

    public function getItemById(int $itemId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM ec_items WHERE ID = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
        return $item ?: null;
    }

    public function getPendingItems(): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_items WHERE STATUS = 'PENDING' ORDER BY CREATED_AT DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public function getItemsByType(string $type, int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT i.* FROM ec_items i 
             LEFT JOIN ec_user_inventory ui ON i.ID = ui.ITEM_ID 
             WHERE i.TYPE = ? AND (ui.USER_ID = ? OR ui.USER_ID IS NULL) AND i.STATUS = 'ACCEPTED'"
        );
        $stmt->bind_param("si", $type, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public function getItems(): array {
        $stmt = $this->db->prepare("SELECT * FROM ec_items WHERE STATUS = 'ACCEPTED' ORDER BY CREATED_AT DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public function createItem(string $name, string $description, int $creatorId, int $createdAt, int $price, string $imageUrl, string $type, bool $isAdminCreated = false): bool {
        $status = $isAdminCreated ? 'ACCEPTED' : 'PENDING';
        $stmt = $this->db->prepare(
            "INSERT INTO ec_items (NAME, DESCRIPTION, CREATOR_ID, CREATED_AT, PRICE, IMAGE_URL, TYPE, STATUS) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssiiisss", $name, $description, $creatorId, $createdAt, $price, $imageUrl, $type, $status);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateItemStatus(int $itemId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE ec_items SET STATUS = ? WHERE ID = ?");
        $stmt->bind_param("si", $status, $itemId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>