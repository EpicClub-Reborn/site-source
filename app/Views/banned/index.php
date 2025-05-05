<?php
$title = 'Account Banned - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6 flex items-center justify-center min-h-screen">
    <!-- Banned Message -->
    <div class="container-shadow p-6 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Account Banned</h1>
        <p class="text-gray-600 mb-4">Your account has been banned. Please contact support for more information.</p>
        <a href="/logout" class="btn-primary py-2 px-4 rounded-md">Logout</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>