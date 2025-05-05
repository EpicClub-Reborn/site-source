<?php
$title = 'Upload Item - EpicClub';
ob_start();
?>
<h1 class="text-2xl font-bold text-gray-200 mb-4">Upload Item</h1>
<p class="text-gray-400 mb-6">Upload a pair of trousers or a shirt for moderation.</p>

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

<div class="bg-gray-800 p-6 rounded-lg shadow-md">
    <div class="flex items-center space-x-4 mb-6">
        <div class="flex items-center space-x-2">
            <i class="fa fa-square text-green-500"></i>
            <p class="text-gray-300">All pixels in this area are <b>Illegal</b></p>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fa fa-square text-yellow-500"></i>
            <p class="text-gray-300">This is your <b>allowed</b> editing area</p>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fa fa-square text-red-500"></i>
            <p class="text-gray-300"><b>Do not edit</b> into this area</p>
        </div>
    </div>

    <div class="flex space-x-4 mb-6">
        <img src="/assets/img/edit_trousers.png" alt="Trousers Template" class="h-48">
        <img src="/assets/img/edit_shirt.png" alt="Shirt Template" class="h-48">
    </div>

    <form method="POST" action="/emporium/upload" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">

        <div>
            <label for="file" class="block text-sm font-medium text-gray-300">Asset File</label>
            <input type="file" id="file" name="file" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-300">Type</label>
            <select id="type" name="type" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="SHIRT">Shirt</option>
                <option value="TROU">Trousers</option>
            </select>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-300">Item Name</label>
            <input type="text" id="name" name="name" maxlength="50" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Item name">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
            <textarea id="description" name="description" maxlength="500" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Description"></textarea>
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-300">Price (Silver)</label>
            <input type="number" id="price" name="price" min="0" value="0" required class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                I am happy with this and will wait for approval by an official
            </button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>