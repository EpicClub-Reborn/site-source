<?php
$title = 'Creations - Admin Panel - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Creations</h1>
        <nav class="flex space-x-4">
            <a href="/admin" class="text-gray-600 hover:text-blue-500">Dashboard</a>
            <a href="/admin/users" class="text-gray-600 hover:text-blue-500">Manage Users</a>
            <a href="/admin/creations" class="text-gray-600 hover:text-blue-500 font-semibold">Creations</a>
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

    <!-- Create Item -->
    <div class="container-shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Item</h2>
        <form method="POST" action="/admin/creations" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
            <input type="hidden" name="action" value="create_item">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-gray-600 mb-1">Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="description" class="block text-gray-600 mb-1">Description</label>
                    <textarea id="description" name="description" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" required></textarea>
                </div>
                <div>
                    <label for="price" class="block text-gray-600 mb-1">Price</label>
                    <input type="number" id="price" name="price" min="0" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="type" class="block text-gray-600 mb-1">Type</label>
                    <select id="type" name="type" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="HAT">Hat</option>
                        <option value="SHIRT">Shirt</option>
                        <option value="PANTS">Pants</option>
                        <option value="FACE">Face</option>
                        <option value="ACCESSORY">Accessory</option>
                        <option value="TOOL">Tool</option>
                        <option value="MASK">Mask</option>
                        <option value="EYES">Eyes</option>
                        <option value="HAIR">Hair</option>
                        <option value="HEAD">Head</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <div>
                    <label for="image" class="block text-gray-600 mb-1">Image</label>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif" class="w-full px-3 py-2 bg-gray-100 text-gray-800 border border-gray-300 rounded-md">
                </div>
                <div>
                    <button type="submit" class="btn-primary py-2 px-4 rounded-md">Create Item</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>