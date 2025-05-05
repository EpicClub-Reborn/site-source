<?php
$title = 'Friends - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Friends</h1>
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

    <!-- Tab Navigation (Breadcrumb Style) -->
    <div class="mb-6">
        <div class="flex space-x-2">
            <a href="/friends?tab=friends" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md <?php echo $tab === 'friends' ? 'bg-gray-100' : 'bg-white'; ?>">
                Friends
            </a>
            <a href="/friends?tab=pending" class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md <?php echo $tab === 'pending' ? 'bg-gray-100' : 'bg-white'; ?>">
                Pending Requests
                <?php if ($pendingCount > 0): ?>
                    <span class="bg-red-500 text-white text-sm px-2 py-1 ml-2 rounded-full"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="container-shadow p-6">
        <?php if ($tab === 'pending'): ?>
            <!-- Pending Friend Requests -->
            <?php if (empty($pendingRequests)): ?>
                <p class="text-gray-500">No pending friend requests.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($pendingRequests as $request): ?>
                        <div class="bg-gray-50 p-4 rounded-md flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="avatar-frame">
                                    <img src="<?php echo htmlspecialchars($request['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-6 h-6 object-contain rounded-full">
                                </div>
                                <div>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($request['USERNAME']); ?></p>
                                    <p class="text-gray-500 text-sm">
                                        Sent on: <span class="request-created-at" data-timestamp="<?php echo $request['CREATED_AT']; ?>"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form method="POST" action="/friends?tab=pending" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                    <input type="hidden" name="action" value="accept_friend">
                                    <input type="hidden" name="request_id" value="<?php echo $request['ID']; ?>">
                                    <button type="submit" class="bg-green-500 text-white py-1 px-2 hover:bg-green-600 rounded-md">Accept</button>
                                </form>
                                <form method="POST" action="/friends?tab=pending" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                    <input type="hidden" name="action" value="reject_friend">
                                    <input type="hidden" name="request_id" value="<?php echo $request['ID']; ?>">
                                    <button type="submit" class="bg-red-500 text-white py-1 px-2 hover:bg-red-600 rounded-md">Reject</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Friends List -->
            <?php if (empty($friends)): ?>
                <p class="text-gray-500">You haven't added any friends yet.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($friends as $friend): ?>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="flex items-center space-x-2">
                                <div class="avatar-frame">
                                    <img src="<?php echo htmlspecialchars($friend['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-6 h-6 object-contain rounded-full">
                                </div>
                                <a href="/user/<?php echo $friend['ID']; ?>" class="text-gray-600 hover:text-blue-500"><?php echo htmlspecialchars($friend['USERNAME']); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/dateUtils.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const requestCreatedAtElements = document.getElementsByClassName('request-created-at');
        for (let element of requestCreatedAtElements) {
            const timestamp = element.getAttribute('data-timestamp');
            element.textContent = formatLocalDate(timestamp);
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>