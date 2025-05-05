<?php
$title = 'Edit Profile - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Profile</h1>
    </div>

    <!-- Profile Edit Form -->
    <div class="container-shadow p-6">
        <form method="POST" action="/profile/edit">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <div class="space-y-4">
                <div>
                    <label for="bio" class="block text-gray-600 mb-1">Bio</label>
                    <textarea id="bio" name="bio" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"><?php echo htmlspecialchars($user['BIO']); ?></textarea>
                </div>
                <div>
                    <label for="status" class="block text-gray-600 mb-1">Status</label>
                    <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($user['STATUS']); ?>" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="50">
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>