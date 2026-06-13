<?php
session_start();

require_once __DIR__ . '/../../../lib/authHelper.php';
require_once __DIR__ . '/../../../controllers/UserController.php';
require_once __DIR__ . '/../../../controllers/ArtTypeController.php';

requireAdmin();

$userController = new UserController();
$artTypeController = new ArtTypeController();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $active = $_POST['active'] ?? '1';
        $role = $_POST['role'] ?? 'Standard';
        $artTypeId = $_POST['artTypeId'] ?? null;

        // Validate inputs
        if (!$firstName || !$lastName || !$email || !$password) {
            throw new Exception('All fields are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User(null, $firstName, $lastName, $email, $passwordHash, $active, $role, $artTypeId);
        $userController->save($user);
        $success = true;
    } catch (Exception $e) {
        $error = 'Error creating user: ' . $e->getMessage();
    }
}

$artTypes = $artTypeController->getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script src="../../../assets/js/tailwind.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <?php if ($success): ?>
        <script>
            setTimeout(() => {
                window.location.href = '../users.php';
            }, 1500);
        </script>
    <?php endif; ?>

    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/nav.php";
                echo navLayout("users", "../../../");
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full relative group/main">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../../components/admin/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto flex-1">
                <div class="container mx-auto rounded-lg shadow-sm p-8">
                    <div class="flex items-center justify-between pb-6 border-b border-gray-200 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Create New User</h1>
                            <p class="mt-1 text-sm text-gray-500">Fill in the details below to create a new user
                                account.</p>
                        </div>
                        <a href="../users.php"
                            class="inline-flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-100 rounded-full p-2 transition">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </a>
                    </div>

                    <?php if ($error): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-red-900">Error</h3>
                                <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <h3 class="font-medium text-green-900">Success</h3>
                                <p class="text-sm text-green-700">User created successfully. Redirecting...</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!$error): ?>
                        <form method="POST" class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="firstName" required placeholder="e.g. John"
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="lastName" required placeholder="e.g. Doe"
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" required placeholder="e.g. john@example.com"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password <span
                                        class="text-red-500">*</span></label>
                                <input type="password" name="password" required placeholder="Enter a strong password"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters recommended.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Role <span
                                            class="text-red-500">*</span></label>
                                    <select name="role" required
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                        <option value="Standard">Standard</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="active" required
                                        class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Art Type</label>
                                <select name="artTypeId"
                                    class="w-full border border-gray-300 rounded-lg p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-600/50 focus:border-indigo-600 transition">
                                    <option value="">Select an art type (optional)</option>
                                    <?php foreach ($artTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>">
                                            <?php echo htmlspecialchars($type['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="pt-6 border-t border-gray-200 flex gap-3">
                                <a href="../users.php"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus:ring-4 focus:ring-gray-200">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition focus:ring-4 focus:ring-indigo-600/20">
                                    Create User
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </section>

    <script src="../../../assets/js/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>