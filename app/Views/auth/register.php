<?php
$title = 'Register - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6 flex items-center justify-center min-h-screen">
    <!-- Register Form -->
    <div class="container-shadow p-6 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Join EpicClub</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="/register">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-600 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="email" class="block text-gray-600 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="password" class="block text-gray-600 mb-1">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="password_confirm" class="block text-gray-600 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="gender" class="block text-gray-600 mb-1">Gender</label>
                    <select id="gender" name="gender" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="MALE" <?php echo (isset($gender) && $gender === 'MALE') ? 'selected' : ''; ?>>Male</option>
                        <option value="FEMALE" <?php echo (isset($gender) && $gender === 'FEMALE') ? 'selected' : ''; ?>>Female</option>
                        <option value="OTHER" <?php echo (isset($gender) && $gender === 'OTHER') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md w-full">Register</button>
                </div>
                <p class="text-gray-600 text-center">Already have an account? <a href="/login" class="text-blue-500 hover:underline">Login</a></p>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>