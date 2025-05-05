<?php
$title = 'Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Admin Panel</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500 font-semibold">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500">Manage Users</a>
            <?php if (!$isModerator): ?>
                <a href="/admin/creations" class="text-gray-600 hover:text-blue-500">Creations</a>
            <?php endif; ?>
            <a href="/admin/audit-logs" class="text-gray-600 hover:text-blue-500">Audit Logs</a>
            <a href="/admin/announcements" class="text-gray-600 hover:text-blue-500">Announcements</a>
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

    <!-- Pending Items Section -->
    <div class="container-shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Pending Items</h2>
        <form method="POST" action="/admin">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <div class="flex items-center space-x-2 mb-4">
                <select name="action" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                    <option value="bulk_approve">Approve Selected</option>
                    <option value="bulk_disapprove">Disapprove Selected</option>
                </select>
                <input type="text" name="bulk_disapprove_reason" placeholder="Reason for disapproval (if disapproving)" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                <button type="submit" class="btn-primary py-2 px-4 rounded-md">Apply</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-gray-800">Select</th>
                            <th class="p-3 text-gray-800">Name</th>
                            <th class="p-3 text-gray-800">Creator</th>
                            <th class="p-3 text-gray-800">Type</th>
                            <th class="p-3 text-gray-800">Image</th>
                            <th class="p-3 text-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingItems as $item): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-3">
                                    <input type="checkbox" name="item_ids[]" value="<?php echo $item['ID']; ?>">
                                </td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($item['NAME']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($this->userModel->findById($item['CREATOR_ID'])['USERNAME'] ?? 'Unknown'); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($item['TYPE']); ?></td>
                                <td class="p-3">
                                    <img src="<?php echo htmlspecialchars($item['IMAGE_URL']); ?>" alt="Item" class="w-12 h-18 object-contain">
                                </td>
                                <td class="p-3 flex space-x-2">
                                    <!-- Approve -->
                                    <form method="POST" action="/admin" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                        <input type="hidden" name="action" value="approve_item">
                                        <input type="hidden" name="item_id" value="<?php echo $item['ID']; ?>">
                                        <button type="submit" class="bg-green-500 text-white py-1 px-2 hover:bg-green-600 rounded-md">Approve</button>
                                    </form>
                                    <!-- Disapprove -->
                                    <form method="POST" action="/admin" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                        <input type="hidden" name="action" value="disapprove_item">
                                        <input type="hidden" name="item_id" value="<?php echo $item['ID']; ?>">
                                        <input type="text" name="disapprove_reason" placeholder="Reason for disapproval" class="bg-gray-100 text-gray-800 p-1 rounded-md border border-gray-300">
                                        <button type="submit" class="bg-red-500 text-white py-1 px-2 hover:bg-red-600 rounded-md ml-2">Disapprove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <!-- IP Bans Section (Hidden for Moderators) -->
    <?php if (!$isModerator): ?>
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">IP Bans</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-gray-800">IP Address</th>
                            <th class="p-3 text-gray-800">Moderator</th>
                            <th class="p-3 text-gray-800">Reason</th>
                            <th class="p-3 text-gray-800">Date</th>
                            <th class="p-3 text-gray-800">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ipBans as $ipBan): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($ipBan['IP']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($this->userModel->findById($ipBan['MOD_ID'])['USERNAME'] ?? 'Unknown'); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($ipBan['REASON']); ?></td>
                                <td class="p-3"><span class="ip-ban-created-at" data-timestamp="<?php echo $ipBan['CREATED_AT']; ?>"></span></td>
                                <td class="p-3">
                                    <form method="POST" action="/admin" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                        <input type="hidden" name="action" value="delete_ip_ban">
                                        <input type="hidden" name="ip_ban_id" value="<?php echo $ipBan['ID']; ?>">
                                        <button type="submit" class="bg-red-500 text-white py-1 px-2 hover:bg-red-600 rounded-md">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="/assets/js/dateUtils.js"></script>
<script>
    // Function to toggle all checkboxes
    function toggleCheckboxes(source, name) {
        const checkboxes = document.getElementsByName(name);
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ipBanCreatedAtElements = document.getElementsByClassName('ip-ban-created-at');
        for (let element of ipBanCreatedAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>