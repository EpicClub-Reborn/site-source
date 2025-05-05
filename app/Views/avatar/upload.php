<?php
$title = 'Upload Asset - EpicClub';
ob_start();
?>
<h1 class="text-2xl font-bold text-gray-800 mb-4">Upload an Asset</h1>
<p class="text-gray-600 mb-6">Upload a pair of trousers or a shirt for the Emporium.</p>

<?php if ($error): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p><?php echo htmlspecialchars($success); ?></p>
    </div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex mb-6 space-x-4">
        <div class="flex items-center space-x-2">
            <i class="fas fa-square text-green-500"></i>
            <span class="text-gray-700 font-medium">Illegal Area</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-square text-yellow-500"></i>
            <span class="text-gray-700 font-medium">Allowed Editing Area</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-square text-red-500"></i>
            <span class="text-gray-700 font-medium">Do Not Edit Area</span>
        </div>
    </div>

    <div class="flex justify-center space-x-6 mb-6">
        <img src="/assets/img/edit_trousers.png" alt="Trousers Template" class="h-40">
        <img src="/assets/img/edit_shirt.png" alt="Shirt Template" class="h-40">
    </div>

    <form method="POST" action="/avatar/upload" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700">Asset File</label>
            <input type="file" id="file" name="file" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select id="type" name="type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="SHIRT">Shirt</option>
                <option value="TROU">Trousers</option>
            </select>
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Item Name (max 50 characters)</label>
            <input type="text" id="name" name="name" maxlength="50" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description (max 500 characters)</label>
            <textarea id="description" name="description" maxlength="500" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Price (Silver)</label>
            <input type="number" id="price" name="price" min="0" value="0" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex justify-center">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                I am happy with this and will wait for approval
            </button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>