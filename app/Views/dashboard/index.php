<?php
$title = 'Dashboard - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Welcome Section with Avatar -->
    <div class="container-shadow p-6 mb-6 flex items-center space-x-6">
        <div class="flex-shrink-0">
            <div class="avatar-frame">
                <img src="<?php echo htmlspecialchars($user['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-[90px] h-[135px] object-contain rounded-lg">
            </div>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, <?php echo htmlspecialchars($user['USERNAME']); ?>!</h1>
            <a href="/avatar/edit" class="btn-primary py-2 px-4 rounded-md">Customize Character</a>
        </div>
    </div>

    <!-- Notifications -->
    <?php if ($notification || $friendRequestNotification): ?>
        <div class="mb-6">
            <?php if ($notification): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                    <p><?php echo htmlspecialchars($notification); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($friendRequestNotification): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md" role="alert">
                    <p><?php echo $friendRequestNotification; ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Announcements Section -->
    <?php if (!empty($announcements)): ?>
        <div class="container-shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Announcements</h2>
            <?php foreach ($announcements as $announcement): ?>
                <div class="bg-gray-50 p-4 mb-4 rounded-md">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($announcement['TITLE']); ?></h3>
                            <p class="text-gray-500 text-sm">
                                Posted on: <span class="announcement-created-at" data-timestamp="<?php echo $announcement['CREATED_AT']; ?>"></span>
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-600"><?php echo htmlspecialchars($announcement['CONTENT']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Status Update and Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Status Update -->
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">What's up, <?php echo htmlspecialchars($user['USERNAME']); ?>?</h2>
            <form method="POST" action="/dashboard/update-status">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                <div class="space-y-4">
                    <div>
                        <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($user['STATUS']); ?>" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="50" required>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary py-2 px-4 rounded-md">Post</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- User Stats -->
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">My Stats</h2>
            <p class="text-gray-600 mb-2"><strong>Gold:</strong> <?php echo $user['GOLD']; ?> <i class="fa fa-coins text-yellow-500 ml-1"></i></p>
            <p class="text-gray-600 mb-2"><strong>Silver:</strong> <?php echo $user['SILVER']; ?> <i class="fa fa-coins text-gray-400 ml-1"></i></p>
            <p class="text-gray-600 mb-2"><strong>Joined:</strong> <span class="join-date" data-timestamp="<?php echo $user['JOINED']; ?>"></span></p>
        </div>

        <!-- Daily Reward (Placeholder) -->
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">My Daily Reward</h2>
            <p class="text-gray-600 mb-2">Next daily reward available in:</p>
            <p class="text-gray-800 text-2xl mb-4">Soon</p>
            <button class="bg-gray-300 text-gray-600 py-2 px-4 rounded-md cursor-not-allowed" disabled>Enable</button>
        </div>
    </div>

    <!-- Friends' Statuses -->
    <div class="container-shadow p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Friends' Statuses</h2>
        <?php if (empty($friendStatuses)): ?>
            <p class="text-gray-500">You haven't added any friends yet.</p>
        <?php else: ?>
            <?php foreach ($friendStatuses as $friend): ?>
                <div class="bg-gray-50 p-4 mb-4 rounded-md">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="avatar-frame">
                            <img src="<?php echo htmlspecialchars($friend['AVATAR_IMG_URL']); ?>" alt="Friend Avatar" class="w-6 h-6 object-contain rounded-full">
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($friend['USERNAME']); ?></h3>
                            <p class="text-gray-500 text-sm">
                                Last online: <span class="friend-last-online" data-timestamp="<?php echo $friend['LAST_ONLINE']; ?>"></span>
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-600"><?php echo htmlspecialchars($friend['STATUS']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/dateUtils.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format Announcement Dates
        const createdAtElements = document.getElementsByClassName('announcement-created-at');
        for (let element of createdAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }

        // Format Join Date
        const joinDateElements = document.getElementsByClassName('join-date');
        for (let element of joinDateElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }

        // Format Friends' Last Online
        const friendLastOnlineElements = document.getElementsByClassName('friend-last-online');
        for (let element of friendLastOnlineElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>