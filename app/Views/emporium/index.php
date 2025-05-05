<?php
$title = 'Emporium - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Emporium</h1>
        <a href="/dashboard" class="text-gray-600 hover:text-blue-500">Back to Dashboard</a>
    </div>

    <!-- Items Grid -->
    <div class="container-shadow p-6">
        <?php if (empty($items)): ?>
            <p class="text-gray-600 text-center">No items are currently available in the Emporium.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($items as $item): ?>
                    <div class="bg-gray-50 p-4 rounded-md text-center">
                        <img src="<?php echo htmlspecialchars($item['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($item['NAME']); ?>" class="w-24 h-24 mx-auto object-contain mb-2">
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($item['NAME']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($item['DESCRIPTION'] ?? 'No description available.'); ?></p>
                        <p class="text-gray-800 font-bold mt-2"><?php echo htmlspecialchars($item['PRICE']); ?> Silver</p>
                        <button class="btn-primary py-2 px-4 rounded-md mt-2">Purchase</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>