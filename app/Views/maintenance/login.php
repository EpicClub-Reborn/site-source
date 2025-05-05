<?php
$title = 'Maintenance Login - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6 flex items-center justify-center min-h-screen">
    <!-- Maintenance Login Form -->
    <div class="container-shadow p-6 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Maintenance Login</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="/maintenance/login">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-600 mb-1">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="password" class="block text-gray-600 mb-1">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md w-full">Login</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>