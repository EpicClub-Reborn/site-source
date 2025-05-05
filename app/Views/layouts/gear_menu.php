<?php
$year = date("Y");
?>
<div id="backDrop" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>
<div id="gearBox" class="hidden bg-white rounded-lg p-6 fixed z-50 left-1/2 transform -translate-x-1/2 top-1/4 shadow-lg">
    <a href="/settings" class="block text-lg text-gray-600 hover:text-blue-500 mb-3">Settings</a>
    <a href="/help" class="block text-lg text-gray-600 hover:text-blue-500 mb-3">Help</a>
    <a href="/avatar/edit" class="block text-lg text-gray-600 hover:text-blue-500 mb-3">Customize</a>
    <a href="/trades" class="block text-lg text-gray-600 hover:text-blue-500 mb-3">Trades</a>
    <a href="/logout" class="block text-lg text-gray-600 hover:text-blue-500 mb-3">Logout</a>
    <a onclick="Gear()" class="block text-sm text-gray-500 hover:text-blue-500 cursor-pointer mb-3">Close</a>
    <p class="text-xs text-gray-400">Rewritten by NokaAngel © <?php echo $year; ?></p>
</div>

<script>
    function Gear() {
        const gearBox = document.getElementById('gearBox');
        const backDrop = document.getElementById('backDrop');
        gearBox.classList.toggle('hidden');
        backDrop.classList.toggle('hidden');
    }

    // Close the gear menu if clicking outside of it
    document.addEventListener('click', function(event) {
        const gearBox = document.getElementById('gearBox');
        const backDrop = document.getElementById('backDrop');
        const gearIcon = document.querySelector('.fa-cog');
        if (!gearBox.contains(event.target) && !gearIcon.contains(event.target)) {
            gearBox.classList.add('hidden');
            backDrop.classList.add('hidden');
        }
    });
</script>