<?php
$title = 'Template - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Template Page</h1>
    </div>

    <!-- Content -->
    <div class="container-shadow p-6">
        <p class="text-gray-600">This is a template page. Add your content here!</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>