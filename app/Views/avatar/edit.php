<?php
$title = 'Customize Avatar - EpicClub';
ob_start();
?>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="container-shadow p-6 mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Customize Avatar</h1>
        <a href="/dashboard" class="text-gray-600 hover:text-blue-500">Back to Dashboard</a>
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

    <!-- Avatar Customization -->
    <div class="container-shadow p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Avatar Preview -->
            <div class="lg:col-span-1 flex flex-col items-center">
                <div class="avatar-frame mb-4">
                    <!-- Advanced 2D Render -->
                    <canvas id="avatarCanvas" width="121" height="181" class="rounded-lg"></canvas>
                </div>
                <form method="POST" action="/avatar/edit" class="w-full">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                    <input type="hidden" name="action" value="reset_avatar">
                    <button type="submit" class="bg-gray-300 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-400 w-full">Reset Avatar</button>
                </form>
            </div>

            <!-- Customization Options -->
            <div class="lg:col-span-2">
                <form method="POST" action="/avatar/edit">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                    <input type="hidden" name="action" value="save_customization">
                    <!-- Hidden inputs to store selected items -->
                    <input type="hidden" id="hat" name="hat" value="<?php echo $customization['HAT_ID'] ?? 0; ?>">
                    <input type="hidden" id="shirt" name="shirt" value="<?php echo $customization['SHIRT_ID'] ?? 0; ?>">
                    <input type="hidden" id="pants" name="pants" value="<?php echo $customization['PANTS_ID'] ?? 0; ?>">
                    <input type="hidden" id="face" name="face" value="<?php echo $customization['FACE_ID'] ?? 0; ?>">
                    <input type="hidden" id="accessory" name="accessory" value="<?php echo $customization['ACCESSORY_ID'] ?? 0; ?>">
                    <input type="hidden" id="tool" name="tool" value="<?php echo $customization['TOOL_ID'] ?? 0; ?>">
                    <input type="hidden" id="mask" name="mask" value="<?php echo $customization['MASK_ID'] ?? 0; ?>">
                    <input type="hidden" id="eyes" name="eyes" value="<?php echo $customization['EYES_ID'] ?? 0; ?>">
                    <input type="hidden" id="hair" name="hair" value="<?php echo $customization['HAIR_ID'] ?? 0; ?>">
                    <input type="hidden" id="head" name="head" value="<?php echo $customization['HEAD_ID'] ?? 0; ?>">
                    <input type="hidden" id="other" name="other" value="<?php echo $customization['OTHER_ID'] ?? 0; ?>">

                    <!-- Breadcrumb Navigation for Categories -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="hats">Hat</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="shirts">Shirt</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="pants">Pants</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="faces">Face</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="accessories">Accessory</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="tools">Tool</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="masks">Mask</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="eyes">Eyes</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="hair">Hair</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="heads">Head</button>
                            <button type="button" class="category-tab px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-100 rounded-md bg-white" data-category="others">Other</button>
                        </div>
                    </div>

                    <!-- Item Display -->
                    <div id="items-display" class="space-y-6">
                        <!-- Hats -->
                        <div id="hats-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hat</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="hat" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($hats as $hat): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['HAT_ID'] == $hat['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="hat" data-id="<?php echo $hat['ID']; ?>" data-name="<?php echo htmlspecialchars($hat['NAME']); ?>" data-image="<?php echo htmlspecialchars($hat['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($hat['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($hat['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($hat['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Hats -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="hat-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentHat = $customization['HAT_ID'] ? array_filter($hats, fn($item) => $item['ID'] == $customization['HAT_ID']) : null;
                                    $currentHat = $currentHat ? array_values($currentHat)[0] : null;
                                    ?>
                                    <?php if ($currentHat): ?>
                                        <img src="<?php echo htmlspecialchars($currentHat['IMAGE_URL']); ?>" alt="Hat" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentHat['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Shirts -->
                        <div id="shirts-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Shirt</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="shirt" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($shirts as $shirt): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['SHIRT_ID'] == $shirt['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="shirt" data-id="<?php echo $shirt['ID']; ?>" data-name="<?php echo htmlspecialchars($shirt['NAME']); ?>" data-image="<?php echo htmlspecialchars($shirt['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($shirt['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($shirt['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($shirt['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Shirts -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="shirt-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentShirt = $customization['SHIRT_ID'] ? array_filter($shirts, fn($item) => $item['ID'] == $customization['SHIRT_ID']) : null;
                                    $currentShirt = $currentShirt ? array_values($currentShirt)[0] : null;
                                    ?>
                                    <?php if ($currentShirt): ?>
                                        <img src="<?php echo htmlspecialchars($currentShirt['IMAGE_URL']); ?>" alt="Shirt" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentShirt['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Pants -->
                        <div id="pants-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Pants</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="pants" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($pants as $pant): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['PANTS_ID'] == $pant['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="pants" data-id="<?php echo $pant['ID']; ?>" data-name="<?php echo htmlspecialchars($pant['NAME']); ?>" data-image="<?php echo htmlspecialchars($pant['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($pant['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($pant['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($pant['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Pants -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="pants-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentPants = $customization['PANTS_ID'] ? array_filter($pants, fn($item) => $item['ID'] == $customization['PANTS_ID']) : null;
                                    $currentPants = $currentPants ? array_values($currentPants)[0] : null;
                                    ?>
                                    <?php if ($currentPants): ?>
                                        <img src="<?php echo htmlspecialchars($currentPants['IMAGE_URL']); ?>" alt="Pants" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentPants['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Faces -->
                        <div id="faces-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Face</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="face" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($faces as $face): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['FACE_ID'] == $face['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="face" data-id="<?php echo $face['ID']; ?>" data-name="<?php echo htmlspecialchars($face['NAME']); ?>" data-image="<?php echo htmlspecialchars($face['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($face['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($face['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($face['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Faces -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="face-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentFace = $customization['FACE_ID'] ? array_filter($faces, fn($item) => $item['ID'] == $customization['FACE_ID']) : null;
                                    $currentFace = $currentFace ? array_values($currentFace)[0] : null;
                                    ?>
                                    <?php if ($currentFace): ?>
                                        <img src="<?php echo htmlspecialchars($currentFace['IMAGE_URL']); ?>" alt="Face" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentFace['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Accessories (Backpack) -->
                        <div id="accessories-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Accessory (Backpack)</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="accessory" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($accessories as $accessory): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['ACCESSORY_ID'] == $accessory['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="accessory" data-id="<?php echo $accessory['ID']; ?>" data-name="<?php echo htmlspecialchars($accessory['NAME']); ?>" data-image="<?php echo htmlspecialchars($accessory['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($accessory['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($accessory['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($accessory['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Accessories -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="accessory-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentAccessory = $customization['ACCESSORY_ID'] ? array_filter($accessories, fn($item) => $item['ID'] == $customization['ACCESSORY_ID']) : null;
                                    $currentAccessory = $currentAccessory ? array_values($currentAccessory)[0] : null;
                                    ?>
                                    <?php if ($currentAccessory): ?>
                                        <img src="<?php echo htmlspecialchars($currentAccessory['IMAGE_URL']); ?>" alt="Accessory" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentAccessory['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tools -->
                        <div id="tools-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tool</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="tool" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($tools as $tool): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['TOOL_ID'] == $tool['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="tool" data-id="<?php echo $tool['ID']; ?>" data-name="<?php echo htmlspecialchars($tool['NAME']); ?>" data-image="<?php echo htmlspecialchars($tool['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($tool['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($tool['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($tool['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Tools -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="tool-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentTool = $customization['TOOL_ID'] ? array_filter($tools, fn($item) => $item['ID'] == $customization['TOOL_ID']) : null;
                                    $currentTool = $currentTool ? array_values($currentTool)[0] : null;
                                    ?>
                                    <?php if ($currentTool): ?>
                                        <img src="<?php echo htmlspecialchars($currentTool['IMAGE_URL']); ?>" alt="Tool" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentTool['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Masks -->
                        <div id="masks-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Mask</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="mask" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($masks as $mask): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['MASK_ID'] == $mask['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="mask" data-id="<?php echo $mask['ID']; ?>" data-name="<?php echo htmlspecialchars($mask['NAME']); ?>" data-image="<?php echo htmlspecialchars($mask['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($mask['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($mask['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($mask['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Masks -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="mask-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentMask = $customization['MASK_ID'] ? array_filter($masks, fn($item) => $item['ID'] == $customization['MASK_ID']) : null;
                                    $currentMask = $currentMask ? array_values($currentMask)[0] : null;
                                    ?>
                                    <?php if ($currentMask): ?>
                                        <img src="<?php echo htmlspecialchars($currentMask['IMAGE_URL']); ?>" alt="Mask" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentMask['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Eyes -->
                        <div id="eyes-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eyes</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="eyes" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($eyes as $eye): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['EYES_ID'] == $eye['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="eyes" data-id="<?php echo $eye['ID']; ?>" data-name="<?php echo htmlspecialchars($eye['NAME']); ?>" data-image="<?php echo htmlspecialchars($eye['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($eye['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($eye['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($eye['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Eyes -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="eyes-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentEyes = $customization['EYES_ID'] ? array_filter($eyes, fn($item) => $item['ID'] == $customization['EYES_ID']) : null;
                                    $currentEyes = $currentEyes ? array_values($currentEyes)[0] : null;
                                    ?>
                                    <?php if ($currentEyes): ?>
                                        <img src="<?php echo htmlspecialchars($currentEyes['IMAGE_URL']); ?>" alt="Eyes" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentEyes['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Hair -->
                        <div id="hair-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Hair</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="hair" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($hair as $hairItem): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['HAIR_ID'] == $hairItem['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="hair" data-id="<?php echo $hairItem['ID']; ?>" data-name="<?php echo htmlspecialchars($hairItem['NAME']); ?>" data-image="<?php echo htmlspecialchars($hairItem['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($hairItem['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($hairItem['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($hairItem['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Hair -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="hair-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentHair = $customization['HAIR_ID'] ? array_filter($hair, fn($item) => $item['ID'] == $customization['HAIR_ID']) : null;
                                    $currentHair = $currentHair ? array_values($currentHair)[0] : null;
                                    ?>
                                    <?php if ($currentHair): ?>
                                        <img src="<?php echo htmlspecialchars($currentHair['IMAGE_URL']); ?>" alt="Hair" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentHair['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Heads -->
                        <div id="heads-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Head</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="head" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($heads as $head): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['HEAD_ID'] == $head['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="head" data-id="<?php echo $head['ID']; ?>" data-name="<?php echo htmlspecialchars($head['NAME']); ?>" data-image="<?php echo htmlspecialchars($head['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($head['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($head['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($head['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Heads -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="head-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentHead = $customization['HEAD_ID'] ? array_filter($heads, fn($item) => $item['ID'] == $customization['HEAD_ID']) : null;
                                    $currentHead = $currentHead ? array_values($currentHead)[0] : null;
                                    ?>
                                    <?php if ($currentHead): ?>
                                        <img src="<?php echo htmlspecialchars($currentHead['IMAGE_URL']); ?>" alt="Head" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentHead['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Others -->
                        <div id="others-items" class="category-items hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Other</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer" data-category="other" data-id="0" data-name="None" data-image="">
                                    <div class="w-12 h-12 mx-auto flex items-center justify-center text-gray-500">None</div>
                                    <p class="text-gray-600 text-sm mt-1">None</p>
                                </div>
                                <?php foreach ($others as $other): ?>
                                    <div class="item-square bg-gray-50 p-2 rounded-md text-center cursor-pointer <?php echo $customization['OTHER_ID'] == $other['ID'] ? 'border-2 border-blue-500' : ''; ?>" data-category="other" data-id="<?php echo $other['ID']; ?>" data-name="<?php echo htmlspecialchars($other['NAME']); ?>" data-image="<?php echo htmlspecialchars($other['IMAGE_URL']); ?>">
                                        <img src="<?php echo htmlspecialchars($other['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($other['NAME']); ?>" class="w-12 h-12 mx-auto object-contain">
                                        <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($other['NAME']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Currently Wearing for Others -->
                            <div class="mt-4">
                                <h3 class="text-sm font-semibold text-gray-600">Currently Wearing</h3>
                                <div id="other-currently-wearing" class="bg-gray-50 p-2 rounded-md mt-1 flex items-center space-x-2">
                                    <?php
                                    $currentOther = $customization['OTHER_ID'] ? array_filter($others, fn($item) => $item['ID'] == $customization['OTHER_ID']) : null;
                                    $currentOther = $currentOther ? array_values($currentOther)[0] : null;
                                    ?>
                                    <?php if ($currentOther): ?>
                                        <img src="<?php echo htmlspecialchars($currentOther['IMAGE_URL']); ?>" alt="Other" class="w-8 h-8 object-contain">
                                        <span class="text-gray-600"><?php echo htmlspecialchars($currentOther['NAME']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn-primary py-2 px-4 rounded-md">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Advanced 2D Render
function renderAvatar() {
    const canvas = document.getElementById('avatarCanvas');
    const ctx = canvas.getContext('2d');

    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Base avatar
    const baseAvatar = new Image();
    baseAvatar.src = '<?php echo htmlspecialchars($user['AVATAR_IMG_URL']); ?>';
    baseAvatar.crossOrigin = "Anonymous"; // Handle cross-origin if needed
    baseAvatar.onload = function() {
        ctx.drawImage(baseAvatar, 0, 0, 121, 181);

        // Draw Hat
        const hatId = document.getElementById('hat').value;
        if (hatId !== '0') {
            const hatImg = new Image();
            hatImg.src = document.querySelector(`.item-square[data-category="hat"][data-id="${hatId}"]`).getAttribute('data-image');
            hatImg.crossOrigin = "Anonymous";
            hatImg.onload = function() {
                ctx.drawImage(hatImg, 0, 0, 121, 181);
            };
            hatImg.onerror = function() {
                console.error('Failed to load hat image:', hatImg.src);
            };
        }

        // Draw Shirt
        const shirtId = document.getElementById('shirt').value;
        if (shirtId !== '0') {
            const shirtImg = new Image();
            shirtImg.src = document.querySelector(`.item-square[data-category="shirt"][data-id="${shirtId}"]`).getAttribute('data-image');
            shirtImg.crossOrigin = "Anonymous";
            shirtImg.onload = function() {
                ctx.drawImage(shirtImg, 0, 0, 121, 181);
            };
            shirtImg.onerror = function() {
                console.error('Failed to load shirt image:', shirtImg.src);
            };
        }

        // Draw Pants
        const pantsId = document.getElementById('pants').value;
        if (pantsId !== '0') {
            const pantsImg = new Image();
            pantsImg.src = document.querySelector(`.item-square[data-category="pants"][data-id="${pantsId}"]`).getAttribute('data-image');
            pantsImg.crossOrigin = "Anonymous";
            pantsImg.onload = function() {
                ctx.drawImage(pantsImg, 0, 0, 121, 181);
            };
            pantsImg.onerror = function() {
                console.error('Failed to load pants image:', pantsImg.src);
            };
        }

        // Draw Face
        const faceId = document.getElementById('face').value;
        if (faceId !== '0') {
            const faceImg = new Image();
            faceImg.src = document.querySelector(`.item-square[data-category="face"][data-id="${faceId}"]`).getAttribute('data-image');
            faceImg.crossOrigin = "Anonymous";
            faceImg.onload = function() {
                ctx.drawImage(faceImg, 0, 0, 121, 181);
            };
            faceImg.onerror = function() {
                console.error('Failed to load face image:', faceImg.src);
            };
        }

        // Draw Accessory
        const accessoryId = document.getElementById('accessory').value;
        if (accessoryId !== '0') {
            const accessoryImg = new Image();
            accessoryImg.src = document.querySelector(`.item-square[data-category="accessory"][data-id="${accessoryId}"]`).getAttribute('data-image');
            accessoryImg.crossOrigin = "Anonymous";
            accessoryImg.onload = function() {
                ctx.drawImage(accessoryImg, 0, 0, 121, 181);
            };
            accessoryImg.onerror = function() {
                console.error('Failed to load accessory image:', accessoryImg.src);
            };
        }

        // Draw Tool
        const toolId = document.getElementById('tool').value;
        if (toolId !== '0') {
            const toolImg = new Image();
            toolImg.src = document.querySelector(`.item-square[data-category="tool"][data-id="${toolId}"]`).getAttribute('data-image');
            toolImg.crossOrigin = "Anonymous";
            toolImg.onload = function() {
                ctx.drawImage(toolImg, 0, 0, 121, 181);
            };
            toolImg.onerror = function() {
                console.error('Failed to load tool image:', toolImg.src);
            };
        }

        // Draw Mask
        const maskId = document.getElementById('mask').value;
        if (maskId !== '0') {
            const maskImg = new Image();
            maskImg.src = document.querySelector(`.item-square[data-category="mask"][data-id="${maskId}"]`).getAttribute('data-image');
            maskImg.crossOrigin = "Anonymous";
            maskImg.onload = function() {
                ctx.drawImage(maskImg, 0, 0, 121, 181);
            };
            maskImg.onerror = function() {
                console.error('Failed to load mask image:', maskImg.src);
            };
        }

        // Draw Eyes
        const eyesId = document.getElementById('eyes').value;
        if (eyesId !== '0') {
            const eyesImg = new Image();
            eyesImg.src = document.querySelector(`.item-square[data-category="eyes"][data-id="${eyesId}"]`).getAttribute('data-image');
            eyesImg.crossOrigin = "Anonymous";
            eyesImg.onload = function() {
                ctx.drawImage(eyesImg, 0, 0, 121, 181);
            };
            eyesImg.onerror = function() {
                console.error('Failed to load eyes image:', eyesImg.src);
            };
        }

        // Draw Hair
        const hairId = document.getElementById('hair').value;
        if (hairId !== '0') {
            const hairImg = new Image();
            hairImg.src = document.querySelector(`.item-square[data-category="hair"][data-id="${hairId}"]`).getAttribute('data-image');
            hairImg.crossOrigin = "Anonymous";
            hairImg.onload = function() {
                ctx.drawImage(hairImg, 0, 0, 121, 181);
            };
            hairImg.onerror = function() {
                console.error('Failed to load hair image:', hairImg.src);
            };
        }

        // Draw Head
        const headId = document.getElementById('head').value;
        if (headId !== '0') {
            const headImg = new Image();
            headImg.src = document.querySelector(`.item-square[data-category="head"][data-id="${headId}"]`).getAttribute('data-image');
            headImg.crossOrigin = "Anonymous";
            headImg.onload = function() {
                ctx.drawImage(headImg, 0, 0, 121, 181);
            };
            headImg.onerror = function() {
                console.error('Failed to load head image:', headImg.src);
            };
        }

        // Draw Other
        const otherId = document.getElementById('other').value;
        if (otherId !== '0') {
            const otherImg = new Image();
            otherImg.src = document.querySelector(`.item-square[data-category="other"][data-id="${otherId}"]`).getAttribute('data-image');
            otherImg.crossOrigin = "Anonymous";
            otherImg.onload = function() {
                ctx.drawImage(otherImg, 0, 0, 121, 181);
            };
            otherImg.onerror = function() {
                console.error('Failed to load other image:', otherImg.src);
            };
        }
    };
    baseAvatar.onerror = function() {
        console.error('Failed to load base avatar image:', baseAvatar.src);
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, 121, 181);
        ctx.fillStyle = '#000';
        ctx.font = '12px Poppins';
        ctx.textAlign = 'center';
        ctx.fillText('Avatar Failed to Load', 60.5, 90.5);
    };
}

// Update Currently Wearing section dynamically
function updateCurrentlyWearing(category, id, name, image) {
    const container = document.getElementById(category + '-currently-wearing');
    const input = document.getElementById(category);
    input.value = id;
    if (id === '0') {
        container.innerHTML = '<span class="text-gray-500">None</span>';
    } else {
        container.innerHTML = `
            <img src="${image}" alt="${category}" class="w-8 h-8 object-contain">
            <span class="text-gray-600">${name}</span>
        `;
    }
    renderAvatar();
}

// Handle Breadcrumb Navigation
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.category-tab');
    const itemSections = document.querySelectorAll('.category-items');

    function showCategory(category) {
        itemSections.forEach(section => section.classList.add('hidden'));
        const targetSection = document.getElementById(`${category}-items`);
        if (targetSection) {
            targetSection.classList.remove('hidden');
        }

        tabs.forEach(tab => {
            tab.classList.remove('bg-gray-100');
            tab.classList.add('bg-white');
        });
        const activeTab = document.querySelector(`.category-tab[data-category="${category}"]`);
        if (activeTab) {
            activeTab.classList.remove('bg-white');
            activeTab.classList.add('bg-gray-100');
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const category = tab.getAttribute('data-category');
            showCategory(category);
        });
    });

    // Show the first category by default
    showCategory('hats');

    // Handle item selection
    const items = document.querySelectorAll('.item-square');
    items.forEach(item => {
        item.addEventListener('click', () => {
            const category = item.getAttribute('data-category');
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            const image = item.getAttribute('data-image');

            // Remove selection highlight from all items in this category
            document.querySelectorAll(`.item-square[data-category="${category}"]`).forEach(i => {
                i.classList.remove('border-2', 'border-blue-500');
            });
            // Add highlight to the selected item
            item.classList.add('border-2', 'border-blue-500');

            updateCurrentlyWearing(category, id, name, image);
        });
    });

    // Initial render
    renderAvatar();
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/layouts/main.php';
?>