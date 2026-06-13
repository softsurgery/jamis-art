<?php
session_start();
require_once __DIR__ . '/../../lib/authHelper.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ArtTypeController.php';

requireAdmin();

$controller = new UserController();
$artTypeController = new ArtTypeController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'create') {
            $user = new User(null, $_POST['firstName'], $_POST['lastName'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['active'], $_POST['role'], $_POST['artTypeId']);
            $controller->save($user);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($action === 'update') {
            $user = new User($_POST['id'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], '', $_POST['active'], $_POST['role'], $_POST['artTypeId']);
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
                    <a href="user/create.php"
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                        + Add New User
                    </a>
                </div>

                <!-- Table -->
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-900">
                            <tr>
                                <th class="px-6 py-4 font-medium">ID</th>
                                <th class="px-6 py-4 font-medium">Name</th>
                                <th class="px-6 py-4 font-medium">Email</th>
                                <th class="px-6 py-4 font-medium">Role</th>
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
                                        $roleLabel = $user['role'] ?? 'Standard';
                                        if (strtolower($roleLabel) === 'admin') {
                                            echo '<span class="inline-flex items-center justify-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs text-purple-700">Admin</span>';
                                        } else {
                                            echo '<span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs text-blue-700">Standard</span>';
                                        }
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

                                                <a href="user/update.php?id=<?= $user['id'] ?>"
                                                    class="w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 text-left flex items-center gap-2 transition">
                                                    <i data-lucide="edit" class="w-4 h-4 text-gray-400"></i> Edit
                                                </a>
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