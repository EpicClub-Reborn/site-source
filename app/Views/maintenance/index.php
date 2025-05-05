<?php
$title = 'Maintenance - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6 flex items-center justify-center min-h-screen">
    <!-- Maintenance Message -->
    <div class="container-shadow p-6 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Site Under Maintenance</h1>
        <p class="text-gray-600 mb-4">We’re performing some maintenance to make EpicClub even better! Please check back soon.</p>
        <a href="/logout" class="btn-primary py-2 px-4 rounded-md">Logout</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>