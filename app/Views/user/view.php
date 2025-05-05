<?php
$title = "Profile - {$user['USERNAME']} - EpicClub";
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Profile Header -->
    <div class="container-shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Avatar -->
            <div class="flex justify-center md:justify-start">
                <div class="avatar-frame">
                    <img src="<?php echo htmlspecialchars($user['AVATAR_IMG_URL']); ?>" alt="Avatar" class="w-[90px] h-[135px] object-contain rounded-lg">
                </div>
            </div>
            <!-- User Info -->
            <div class="col-span-2">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($user['USERNAME']); ?>
                    <?php if ($user['POWER'] === 'ADMIN'): ?>
                        <i class="fa fa-crown text-yellow-500 ml-2" title="Admin"></i>
                    <?php elseif ($user['POWER'] === 'MODERATOR'): ?>
                        <i class="fa fa-shield-alt text-gray-400 ml-2" title="Moderator"></i>
                    <?php endif; ?>
                </h1>
                <div class="flex items-center space-x-2 text-gray-500 mb-2">
                    <i class="fa fa-calendar"></i>
                    <span>Joined: <span class="join-date" data-timestamp="<?php echo $user['JOINED']; ?>"></span></span>
                </div>
                <div class="flex items-center space-x-2 text-gray-500 mb-2">
                    <i class="fa fa-user-friends"></i>
                    <span>Friends: <?php echo $this->userModel->countFriends($user['ID']); ?></span>
                </div>
                <!-- Add Friend Button -->
                <form method="POST" action="/user/<?php echo $user['ID']; ?>" class="mt-2">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                    <input type="hidden" name="action" value="add_friend">
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Add Friend</button>
                </form>
            </div>
        </div>
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

    <!-- Bio and Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Bio -->
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">About Me</h2>
            <p class="text-gray-600"><?php echo htmlspecialchars($user['BIO']); ?></p>
        </div>
        <!-- Status -->
        <div class="container-shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Status</h2>
            <p class="text-gray-600"><?php echo htmlspecialchars($user['STATUS']); ?></p>
        </div>
    </div>

    <!-- Ban Reasons (Admin Only) -->
    <?php if (!empty($banReasons)): ?>
        <div class="container-shadow p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ban Reasons</h2>
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
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const banStartTimeElements = document.getElementsByClassName('ban-start-time');
                for (let element of banStartTimeElements) {
                    const timestamp = element.getAttribute('data-timestamp');
                    element.textContent = formatLocalDate(timestamp);
                }
            });
        </script>
    <?php endif; ?>
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