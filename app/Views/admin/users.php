<?php
$title = 'Manage Users - Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Manage Users</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500 font-semibold">Manage Users</a>
            <?php if (!$isModerator): ?>
                <a href="/admin/creations" class="text-gray-600 hover:text-blue-500">Creations</a>
            <?php endif; ?>
            <a href="/admin/audit-logs" class="text-gray-600 hover:text-blue-500">Audit Logs</a>
            <a href="/admin/announcements" class="text-gray-600 hover:text-blue-500">Announcements</a>
            <a href="/admin/settings" class="text-gray-600 hover:text-blue-500">Settings</a>
        </nav>
    </div>

    <!-- Users Table -->
    <div class="container-shadow p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-gray-800">Username</th>
                        <th class="p-3 text-gray-800">Join Date</th>
                        <th class="p-3 text-gray-800">Avatar</th>
                        <th class="p-3 text-gray-800">Bio</th>
                        <th class="p-3 text-gray-800">Status</th>
                        <?php if (!$isModerator): ?>
                            <th class="p-3 text-gray-800">IP</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/users/view/<?php echo $user['ID']; ?>'">
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($user['USERNAME']); ?></td>
                            <td class="p-3"><span class="join-date" data-timestamp="<?php echo $user['JOINED']; ?>"></span></td>
                            <td class="p-3">
                                <div class="avatar-frame">
                                    <img src="<?php echo htmlspecialchars($user['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-6 h-6 object-contain rounded-full">
                                </div>
                            </td>
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($user['BIO']); ?></td>
                            <td class="p-3 text-gray-600"><?php echo htmlspecialchars($user['STATUS']); ?></td>
                            <?php if (!$isModerator): ?>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($user['IP']); ?></td>
                            <?php endif; ?>
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
        const joinDateElements = document.getElementsByClassName('join-date');
        for (let element of joinDateElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>