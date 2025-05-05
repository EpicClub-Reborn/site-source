<?php
namespace App\Controllers;

class HomeController {
    public function index(): void {
        if (isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        $title = 'Welcome to EpicClub';
        require BASE_PATH . '/app/Views/home/index.php';
    }
}
?>