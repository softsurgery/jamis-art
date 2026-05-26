<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col flex-1 h-screen w-screen overflow-hidden">
    <section class="flex flex-row flex-1 overflow-hidden">
        <div class="flex flex-col">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/nav.php";
                echo navLayout();
                ?>
            </nav>
        </div>
        <main class="flex flex-col flex-1 overflow-hidden w-full">
            <header class="p-4">
                <?php
                require_once __DIR__ . "/../../components/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto container mx-auto p-8 bg-gray-50 flex-1">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                        <p class="mt-1 text-sm text-gray-500">Manage all registered users, roles, and status.</p>
                    </div>
                    <button
                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                        + Add New User
                    </button>
                </div>

                <?php
                require_once __DIR__ . "/../../components/user-table.php";
                echo userTableLayout();
                ?>
            </div>

        </main>
    </section>
</body>

</html>