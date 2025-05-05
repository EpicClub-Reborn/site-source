<?php
$title = 'Settings - Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Settings</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500">Manage Users</a>
            <?php if (!$isModerator): ?>
                <a href="/admin/creations" class="text-gray-600 hover:text-blue-500">Creations</a>
            <?php endif; ?>
            <a href="/admin/audit-logs" class="text-gray-600 hover:text-blue-500">Audit Logs</a>
            <a href="/admin/announcements" class="text-gray-600 hover:text-blue-500">Announcements</a>
            <a href="/admin/settings" class="text-gray-600 hover:text-blue-500 font-semibold">Settings</a>
        </nav>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <!-- Settings Form -->
    <div class="container-shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Site Settings</h2>
        <form method="POST" action="/admin/settings">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <input type="hidden" name="action" value="update_settings">
            <div class="space-y-4">
                <div>
                    <label for="maintenance_mode" class="block text-gray-600 mb-1">Maintenance Mode</label>
                    <select id="maintenance_mode" name="settings[maintenance_mode]" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="off" <?php echo ($settings['maintenance_mode'] ?? 'off') === 'off' ? 'selected' : ''; ?>>Off</option>
                        <option value="on" <?php echo ($settings['maintenance_mode'] ?? 'off') === 'on' ? 'selected' : ''; ?>>On</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Save Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>