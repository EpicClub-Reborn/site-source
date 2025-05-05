<?php
$title = 'Announcements - Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Announcements</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500">Manage Users</a>
            <?php if (!$isModerator): ?>
                <a href="/admin/creations" class="text-gray-600 hover:text-blue-500">Creations</a>
            <?php endif; ?>
            <a href="/admin/audit-logs" class="text-gray-600 hover:text-blue-500">Audit Logs</a>
            <a href="/admin/announcements" class="text-gray-600 hover:text-blue-500 font-semibold">Announcements</a>
            <a href="/admin/settings" class="text-gray-600 hover:text-blue-500">Settings</a>
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

    <!-- Create Announcement -->
    <div class="container-shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Announcement</h2>
        <form method="POST" action="/admin/announcements">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <input type="hidden" name="action" value="create_announcement">
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-600 mb-1">Title</label>
                    <input type="text" id="title" name="title" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="content" class="block text-gray-600 mb-1">Content</label>
                    <textarea id="content" name="content" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" required></textarea>
                </div>
                <div>
                    <label for="duration" class="block text-gray-600 mb-1">Duration (days)</label>
                    <input type="number" id="duration" name="duration" min="1" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Create Announcement</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Announcements List -->
    <div class="container-shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">All Announcements</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-gray-800">Title</th>
                        <th class="p-3 text-gray-800">Content</th>
                        <th class="p-3 text-gray-800">Created By</th>
                        <th class="p-3 text-gray-800">Created At</th>
                        <th class="p-3 text-gray-800">Expires At</th>
                        <th class="p-3 text-gray-800">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($announcement['TITLE']); ?></td>
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($announcement['CONTENT']); ?></td>
                            <td class="p-3">
                                <?php
                                $creator = $this->userModel->findById($announcement['CREATED_BY']);
                                echo htmlspecialchars($creator['USERNAME'] ?? 'Unknown');
                                ?>
                            </td>
                            <td class="p-3"><span class="announcement-created-at" data-timestamp="<?php echo $announcement['CREATED_AT']; ?>"></span></td>
                            <td class="p-3"><span class="announcement-expires-at" data-timestamp="<?php echo $announcement['EXPIRES_AT']; ?>"></span></td>
                            <td class="p-3">
                                <form method="POST" action="/admin/announcements" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                    <input type="hidden" name="action" value="delete_announcement">
                                    <input type="hidden" name="announcement_id" value="<?php echo $announcement['ID']; ?>">
                                    <button type="submit" class="bg-red-500 text-white py-1 px-2 hover:bg-red-600 rounded-md">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/assets/js/dateUtils.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createdAtElements = document.getElementsByClassName('announcement-created-at');
        for (let element of createdAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
        const expiresAtElements = document.getElementsByClassName('announcement-expires-at');
        for (let element of expiresAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>