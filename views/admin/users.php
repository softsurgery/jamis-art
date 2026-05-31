<?php
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

$controller = new UserController();
$artTypeController = new ArtTypeController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $user = new User(null, $_POST['firstName'], $_POST['lastName'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['active'], $_POST['artTypeId']);
            $controller->save($user);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'update') {
            $user = new User($_POST['id'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], '', $_POST['active'], $_POST['artTypeId']);
            $controller->update($_POST['id'], $user);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'delete') {
            $controller->delete($_POST['id']);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'update_status') {
            $controller->updateStatus($_POST['id'], $_POST['status']);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

$users = $controller->getAll();
$artTypes = $artTypeController->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/nav.php";
                echo navLayout("users", "../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage all registered users, roles, and status.</p>
                    </div>
                    <button onclick="openSheet('create')"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                        + Add New User
                    </button>
                </div>

                <!-- Table -->
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Name</th>
                                <th class="px-6 py-4 font-medium">Email</th>
                                <th class="px-6 py-4 font-medium">Art</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50 transition relative">
                                    <td class="px-6 py-4"><?= htmlspecialchars($user['id']) ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $artLabel = 'N/A';
                                        foreach ($artTypes as $type) {
                                            if ($type['id'] == $user['artTypeId']) {
                                                $artLabel = $type['label'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($artLabel);
                                        ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($user['active'] == 1): ?>
                                            <span
                                                class='inline-flex items-center justify-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs text-emerald-700'>Active</span>
                                        <?php else: ?>
                                            <span
                                                class='inline-flex items-center justify-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-700'>Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-block relative">
                                            <button onclick="toggleDropdown(<?= $user['id'] ?>)"
                                                class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                            </button>
                                            <!-- dropdown -->
                                            <div id="dropdown-<?= $user['id'] ?>"
                                                class="hidden absolute right-0 top-full mt-1 z-20 w-36 bg-white rounded-lg shadow-lg border border-gray-100 py-1 text-left overflow-hidden">

                                                <?php if ($user['active'] == 1): ?>
                                                    <form method="POST" class="w-full m-0 p-0">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit"
                                                            class="w-full px-4 py-2.5 text-sm text-yellow-600 hover:bg-yellow-50 text-left flex items-center gap-2 transition">
                                                            <i data-lucide="user-x" class="w-4 h-4 text-yellow-400"></i>
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" class="w-full m-0 p-0">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit"
                                                            class="w-full px-4 py-2.5 text-sm text-emerald-600 hover:bg-emerald-50 text-left flex items-center gap-2 transition">
                                                            <i data-lucide="user-check" class="w-4 h-4 text-emerald-400"></i>
                                                            Activate
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <button
                                                    onclick="openSheet('update', <?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['firstName'])) ?>', '<?= htmlspecialchars(addslashes($user['lastName'])) ?>', '<?= htmlspecialchars(addslashes($user['email'])) ?>', '<?= htmlspecialchars(addslashes($user['active'])) ?>', '<?= htmlspecialchars(addslashes($user['artTypeId'])) ?>')"
                                                    class="w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="edit" class="w-4 h-4 text-gray-400"></i> Edit
                                                </button>
                                                <button onclick="openDialog(<?= $user['id'] ?>)"
                                                    class="w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i> Delete
                                                </button>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No users found. Create one to get started.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </section>

    <!-- Side Sheet for Create/Update -->
    <div id="side-sheet"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden justify-end transition-opacity">
        <div class="bg-white w-[400px] h-full shadow-2xl p-6 flex flex-col translate-x-full transition-transform duration-300 ease-out"
            id="sheet-content">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                <h2 id="sheet-title" class="text-xl font-semibold text-gray-900">Add User</h2>
                <button onclick="closeSheet()"
                    class="p-2 text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded-full transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form method="POST" id="sheet-form" class="flex flex-col gap-5 flex-1">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id" value="">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">First Name</label>
                    <input type="text" name="firstName" id="form-firstName" required placeholder="e.g. John"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Last Name</label>
                    <input type="text" name="lastName" id="form-lastName" required placeholder="e.g. Doe"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="form-email" required placeholder="john@example.com"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" id="form-password"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <select name="active" id="form-active" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role (Art Type)</label>
                    <select name="artTypeId" id="form-artTypeId" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                        <option value="">Select Role/Type</option>
                        <?php foreach ($artTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mt-auto pt-6 border-t border-gray-100">
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white rounded-lg py-2.5 font-medium hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div id="delete-dialog"
        class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm scale-95 opacity-0 transition-all duration-200"
            id="dialog-content">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete User?</h2>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed">Are you sure you want to delete this user? This
                action cannot be undone.</p>

            <form method="POST" class="flex items-center justify-end gap-3">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete-id" value="">
                <button type="button" onclick="closeDialog()"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">Cancel</button>
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition focus:ring-4 focus:ring-red-600/20">Delete</button>
            </form>
        </div>
    </div>

    <script src="../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();

        // Dropdown Logic
        function toggleDropdown(id) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== 'dropdown-' + id) el.classList.add('hidden');
            });
            document.getElementById('dropdown-' + id).classList.toggle('hidden');
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.inline-block.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
            }
        });

        // Sheet Logic
        const sheet = document.getElementById('side-sheet');
        const sheetContent = document.getElementById('sheet-content');

        function openSheet(action, id = '', firstName = '', lastName = '', email = '', active = '', artTypeId = '') {
            document.getElementById('form-action').value = action;
            document.getElementById('form-id').value = id;
            document.getElementById('form-firstName').value = firstName;
            document.getElementById('form-lastName').value = lastName;
            document.getElementById('form-email').value = email;
            document.getElementById('form-active').value = active;

            if (action === 'create') {
                document.getElementById('form-password').setAttribute('required', 'required');
            } else {
                document.getElementById('form-password').removeAttribute('required');
            }

            if (artTypeId) {
                document.getElementById('form-artTypeId').value = artTypeId;
            } else {
                document.getElementById('form-artTypeId').value = "";
            }

            document.getElementById('sheet-title').innerText = action === 'create' ? 'Add User' : 'Edit User';

            sheet.classList.remove('hidden');
            sheet.classList.add('flex');

            // Allow display:flex to apply before translating
            requestAnimationFrame(() => {
                sheetContent.classList.remove('translate-x-full');
            });

            // Close dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        }

        function closeSheet() {
            sheetContent.classList.add('translate-x-full');
            setTimeout(() => {
                sheet.classList.add('hidden');
                sheet.classList.remove('flex');
            }, 300);
        }

        // Click outside sheet to close
        sheet.addEventListener('click', (e) => {
            if (e.target === sheet) closeSheet();
        });

        // Dialog Logic
        const dialog = document.getElementById('delete-dialog');
        const dialogContent = document.getElementById('dialog-content');

        function openDialog(id) {
            document.getElementById('delete-id').value = id;
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');

            requestAnimationFrame(() => {
                dialogContent.classList.remove('scale-95', 'opacity-0');
                dialogContent.classList.add('scale-100', 'opacity-100');
            });

            // Close dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
        }

        function closeDialog() {
            dialogContent.classList.remove('scale-100', 'opacity-100');
            dialogContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                dialog.classList.add('hidden');
                dialog.classList.remove('flex');
            }, 200);
        }

        // Click outside dialog to close
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) closeDialog();
        });
    </script>
</body>

</html>