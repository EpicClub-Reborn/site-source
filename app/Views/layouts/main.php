<?php
$user = null;
if (isLoggedIn()) {
    $userModel = new App\Models\User();
    $user = $userModel->findByUsername($_SESSION['username']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/assets/js/functions.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa 0%, #fffde7 100%);
            color: #333;
        }
        .sidebar-link {
            transition: all 0.3s ease;
        }
        .sidebar-link:hover {
            transform: translateX(5px);
            background: linear-gradient(to right, #ff6f61, #ff8e53);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(to right, #ff6f61, #ff8e53);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #ff8e53, #ff6f61);
            transform: scale(1.05);
        }
        .avatar-frame {
            background: linear-gradient(45deg, #ffcccb, #ffebee);
            border-radius: 50%;
            padding: 4px;
            display: inline-block;
        }
        .container-shadow {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-poppins">
    <!-- Header -->
    <header class="bg-white shadow fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="/assets/img/EpicClub.png" alt="EpicClub Logo" class="h-8">
                <h1 class="text-xl font-bold text-gray-800">EpicClub</h1>
            </div>
            <?php if ($user): ?>
                <div class="flex items-center space-x-4">
                    <div class="text-gray-600 text-sm">
                        Hey, <?php echo htmlspecialchars($user['USERNAME']); ?>!
                    </div>
                    <div class="flex items-center space-x-2 bg-gray-100 rounded-full px-4 py-1">
                        <a href="/user/convert" class="text-gray-800 flex items-center space-x-1">
                            <span><?php echo htmlspecialchars($user['GOLD']); ?></span>
                            <i class="fa fa-circle text-yellow-500"></i>
                            <span class="ml-2"><?php echo htmlspecialchars($user['SILVER']); ?></span>
                            <i class="fa fa-circle text-gray-400"></i>
                        </a>
                    </div>
                    <div class="flex items-center space-x-3 text-lg">
                        <!-- Messages -->
                        <?php
                        $hasMessages = $userModel->hasUnreadMessages($user['ID']);
                        ?>
                        <i onclick="GoToM()" class="fa fa-comment cursor-pointer <?php echo $hasMessages ? 'text-red-500' : 'text-gray-600'; ?> hover:text-blue-500"></i>
                        <!-- Notifications (for admins) -->
                        <?php if ($user['POWER'] !== 'MEMBER' && $user['POWER'] !== 'MODERATOR'): ?>
                            <?php
                            $pendingAssets = $userModel->countPendingAssets();
                            ?>
                            <a href="/admin" class="relative" title="Moderation Panel">
                                <i class="fa fa-gavel text-gray-600 hover:text-blue-500"></i>
                                <?php if ($pendingAssets > 0): ?>
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1"><?php echo $pendingAssets; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <!-- Gear Menu -->
                        <i onclick="Gear()" class="fa fa-cog text-gray-600 cursor-pointer hover:text-blue-500"></i>
                        <!-- Profile -->
                        <a href="/user/<?php echo $user['ID']; ?>">
                            <i class="fa fa-user text-gray-600 hover:text-blue-500"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Include Gear Menu -->
    <?php if ($user): ?>
        <?php require BASE_PATH . '/app/Views/layouts/gear_menu.php'; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="flex min-h-screen pt-16">
        <!-- Sidebar -->
        <aside class="bg-white w-64 p-4 fixed h-full shadow-lg">
            <nav class="space-y-2">
                <a href="/dashboard" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-tachometer"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/emporium" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Emporium</span>
                </a>
                <a href="/play" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-gamepad"></i>
                    <span>Play</span>
                </a>
                <a href="/forums" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-comments"></i>
                    <span>Forums</span>
                </a>
                <a href="/groups" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-users"></i>
                    <span>Groups</span>
                </a>
                <a href="/upgrade" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-star"></i>
                    <span>Upgrade</span>
                </a>
                <a href="/friends" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-user-friends"></i>
                    <span>Friends</span>
                    <span class="ml-auto bg-blue-500 text-white text-xs font-bold rounded-full px-2 py-1"><?php echo $user ? $userModel->countFriends($user['ID']) : 0; ?></span>
                </a>
                <a href="/trades" class="sidebar-link flex items-center space-x-2 text-gray-600 hover:text-white p-2 rounded-md">
                    <i class="fa fa-exchange"></i>
                    <span>Trades</span>
                </a>
            </nav>
            <div class="mt-4">
                <h3 class="text-gray-600 text-sm font-semibold mb-2">Friends</h3>
                <div class="space-y-2">
                    <?php
                    if ($user) {
                        $friends = $userModel->getFriends($user['ID'], 5);
                        if (empty($friends)) {
                            echo '<p class="text-gray-500">No friends yet.</p>';
                        } else {
                            foreach ($friends as $friend) {
                                echo '<div class="flex items-center space-x-2">';
                                echo '<div class="avatar-frame">';
                                echo '<img src="' . htmlspecialchars($friend['AVATAR_IMG_URL']) . '" alt="Friend Avatar" class="w-6 h-6 object-contain rounded-full">';
                                echo '</div>';
                                echo '<a href="/user/' . $friend['ID'] . '" class="text-gray-600 hover:text-blue-500">' . htmlspecialchars($friend['USERNAME']) . '</a>';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-6">
            <?php echo $content; ?>
        </main>
    </div>
</body>
</html>