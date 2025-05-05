<?php
namespace App\Controllers;

use App\Models\Item;

class EmporiumController {
    private Item $itemModel;

    public function __construct() {
        $this->itemModel = new Item();
    }

    public function index(): void {
        // Fetch all accepted items for the Emporium
        $items = $this->itemModel->getItems();

        $title = 'Emporium - EpicClub';
        require BASE_PATH . '/app/Views/emporium/index.php';
    }
}
?>