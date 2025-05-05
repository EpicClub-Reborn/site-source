<?php
$title = 'Audit Logs - Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Audit Logs</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500">Manage Users</a>
            <?php if (!$isModerator): ?>
                <a href="/admin/creations" class="text-gray-600 hover:text-blue-500">Creations</a>
            <?php endif; ?>
            <a href="/admin/audit-logs" class="text-gray-600 hover:text-blue-500 font-semibold">Audit Logs</a>
            <a href="/admin/announcements" class="text-gray-600 hover:text-blue-500">Announcements</a>
            <a href="/admin/settings" class="text-gray-600 hover:text-blue-500">Settings</a>
        </nav>
    </div>

    <!-- Audit Logs Table -->
    <div class="container-shadow p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-gray-800">Action Type</th>
                        <th class="p-3 text-gray-800">User Affected</th>
                        <th class="p-3 text-gray-800">Moderator</th>
                        <th class="p-3 text-gray-800">Details</th>
                        <th class="p-3 text-gray-800">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($log['ACTION_TYPE']); ?></td>
                            <td class="p-3">
                                <?php
                                $affectedUser = $log['USER_ID'] ? $this->userModel->findById($log['USER_ID'], true) : null;
                                echo $affectedUser ? htmlspecialchars($affectedUser['USERNAME']) : 'N/A';
                                ?>
                            </td>
                            <td class="p-3">
                                <?php
                                $mod = $this->userModel->findById($log['MOD_ID'], true);
                                echo htmlspecialchars($mod['USERNAME'] ?? 'Unknown');
                                ?>
                            </td>
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($log['DETAILS'] ?? 'No details'); ?></td>
                            <td class="p-3"><span class="log-created-at" data-timestamp="<?php echo $log['CREATED_AT']; ?>"></span></td>
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
        const logCreatedAtElements = document.getElementsByClassName('log-created-at');
        for (let element of logCreatedAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>