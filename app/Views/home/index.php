<?php
$title = 'Welcome to EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6 flex items-center justify-center min-h-screen">
    <!-- Welcome Section -->
    <div class="container-shadow p-6 text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to EpicClub!</h1>
        <p class="text-gray-600 mb-6">Join our fun community to customize your avatar, make friends, and explore exciting games!</p>
        <div class="flex justify-center space-x-4">
            <a href="/login" class="btn-primary py-2 px-4 rounded-md">Login</a>
            <a href="/register" class="bg-gray-100 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-200">Register</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>