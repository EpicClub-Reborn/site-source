<?php
$title = "User {$user['USERNAME']} - Admin Panel - EpicClub";
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6 relative">
        <a href="/admin/users" class="absolute top-4 right-4 text-gray-600 hover:text-blue-500 text-xl">×</a>
        <h1 class="text-2xl font-bold text-gray-800 mb-4">User: <?php echo htmlspecialchars($user['USERNAME']); ?></h1>
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

    <!-- User Details -->
    <div class="container-shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-gray-600 mb-2"><strong>Join Date:</strong> <span class="join-date" data-timestamp="<?php echo $user['JOINED']; ?>"></span></p>
                <p class="text-gray-600 mb-2"><strong>Bio:</strong> <?php echo htmlspecialchars($user['BIO']); ?></p>
                <p class="text-gray-600 mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($user['STATUS']); ?></p>
                <?php if (!$isModerator): ?>
                    <p class="text-gray-600 mb-2"><strong>IP:</strong> <?php echo htmlspecialchars($user['IP']); ?></p>
                <?php endif; ?>
                <p class="text-gray-600 mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['POWER']); ?></p>
            </div>
            <div class="flex justify-center">
                <div class="avatar-frame">
                    <img src="<?php echo htmlspecialchars($user['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-[90px] h-[135px] object-contain rounded-lg">
                </div>
            </div>
        </div>
    </div>

    <!-- Role Management (Hidden for Moderators if User is ADMIN or MODERATOR) -->
    <?php if (!$isModerator || ($isModerator && $user['POWER'] === 'MEMBER')): ?>
        <div class="container-shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Manage Role</h2>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <div class="flex items-center space-x-2">
                    <select name="role" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                        <option value="MEMBER" <?php echo $user['POWER'] === 'MEMBER' ? 'selected' : ''; ?>>Member</option>
                        <option value="MODERATOR" <?php echo $user['POWER'] === 'MODERATOR' ? 'selected' : ''; ?>>Moderator</option>
                        <option value="ADMIN" <?php echo $user['POWER'] === 'ADMIN' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Change Role</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Ban Reasons -->
    <div class="container-shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Ban Reasons</h2>
        <?php if (empty($banReasons)): ?>
            <p class="text-gray-500">No active bans.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-gray-800">Reason</th>
                            <th class="p-3 text-gray-800">Start Date</th>
                            <th class="p-3 text-gray-800">Duration</th>
                            <th class="p-3 text-gray-800">Moderator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banReasons as $ban): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($ban['REASON']); ?></td>
                                <td class="p-3"><span class="ban-start-time" data-timestamp="<?php echo $ban['START_TIME']; ?>"></span></td>
                                <td class="p-3 text-gray-600"><?php echo $ban['LENGTH'] === -11122000 ? 'Permanent' : ($ban['LENGTH'] / 86400) . ' days'; ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($this->userModel->findById($ban['MOD_ID'], true)['USERNAME'] ?? 'Unknown'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="mt-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="unban_user">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <button type="submit" class="btn-primary py-2 px-4 rounded-md">Unban User</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Notes -->
    <div class="container-shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Notes</h2>
        <?php if (empty($notes)): ?>
            <p class="text-gray-500">No notes or warnings.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-gray-800">Type</th>
                            <th class="p-3 text-gray-800">Content</th>
                            <th class="p-3 text-gray-800">Moderator</th>
                            <th class="p-3 text-gray-800">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $note): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($note['NOTE_TYPE']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($note['CONTENT']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($this->userModel->findById($note['MOD_ID'], true)['USERNAME'] ?? 'Unknown'); ?></td>
                                <td class="p-3"><span class="note-created-at" data-timestamp="<?php echo $note['CREATED_AT']; ?>"></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="container-shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
        <?php if (empty($recentActivity)): ?>
            <p class="text-gray-500">No recent activity.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-gray-800">Event Type</th>
                            <th class="p-3 text-gray-800">Details</th>
                            <th class="p-3 text-gray-800">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivity as $event): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($event['EVENT_TYPE']); ?></td>
                                <td class="p-3 text-gray-600"><?php echo htmlspecialchars($event['EVENT_DATA'] ?? 'No details'); ?></td>
                                <td class="p-3"><span class="activity-created-at" data-timestamp="<?php echo $event['CREATED_AT']; ?>"></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="container-shadow p-6 space-y-4">
        <!-- Ban User -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Ban User</h3>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="ban_user">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <div class="flex items-center space-x-2">
                    <select name="reason_type" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                        <option value="Spamming">Spamming</option>
                        <option value="Harassment">Harassment</option>
                        <option value="Cheating">Cheating</option>
                        <option value="custom">Custom Reason</option>
                    </select>
                    <input type="text" name="custom_reason" placeholder="Custom reason" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                    <select name="duration" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300">
                        <option value="86400">1 Day</option>
                        <option value="604800">1 Week</option>
                        <option value="2592000">1 Month</option>
                        <?php if (!$isModerator): ?>
                            <option value="Permanent">Permanent</option>
                        <?php endif; ?>
                    </select>
                    <button type="submit" class="bg-red-500 text-white py-2 px-4 hover:bg-red-600 rounded-md">Ban</button>
                </div>
            </form>
        </div>

        <!-- Warn User -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Warn User</h3>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="warn_user">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <div class="flex items-center space-x-2">
                    <input type="text" name="warning" placeholder="Warning message" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300 w-full">
                    <button type="submit" class="bg-yellow-500 text-white py-2 px-4 hover:bg-yellow-600 rounded-md">Warn</button>
                </div>
            </form>
        </div>

        <!-- Add Note -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Add Note</h3>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="add_note">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <div class="flex items-center space-x-2">
                    <input type="text" name="note" placeholder="Add note" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300 w-full">
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Add Note</button>
                </div>
            </form>
        </div>

        <!-- IP Ban (Hidden for Moderators) -->
        <?php if (!$isModerator): ?>
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">IP Ban</h3>
                <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                    <input type="hidden" name="action" value="ip_ban">
                    <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="ip_ban_reason" placeholder="IP ban reason" class="bg-gray-100 text-gray-800 p-2 rounded-md border border-gray-300 w-full">
                        <button type="submit" class="bg-red-500 text-white py-2 px-4 hover:bg-red-600 rounded-md">IP Ban</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Reset Avatar -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Reset Avatar</h3>
            <form method="POST" action="/admin/users/view/<?php echo $user['ID']; ?>" class="space-y-2">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <input type="hidden" name="action" value="reset_avatar">
                <input type="hidden" name="user_id" value="<?php echo $user['ID']; ?>">
                <button type="submit" class="bg-purple-500 text-white py-2 px-4 hover:bg-purple-600 rounded-md">Reset Avatar</button>
            </form>
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
        const banStartTimeElements = document.getElementsByClassName('ban-start-time');
        for (let element of banStartTimeElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
        const noteCreatedAtElements = document.getElementsByClassName('note-created-at');
        for (let element of noteCreatedAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
        const activityCreatedAtElements = document.getElementsByClassName('activity-created-at');
        for (let element of activityCreatedAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>