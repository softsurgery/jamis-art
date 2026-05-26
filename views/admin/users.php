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
        <div class="flex flex-col border">
            <nav class="flex flex-col w-[15vw] h-full p-4">
                <?php
                require_once __DIR__ . "/../../components/nav.php";
                echo navLayout();
                ?>
            </nav>
        </div>
        <main class="w-full border flex flex-col flex-1 overflow-hidden">
            <header class="border p-4">
                <?php
                require_once __DIR__ . "/../../components/header.php";
                echo headerLayout();
                ?>
            </header>
            <div class="overflow-auto p-4">
                <h1>Users</h1>

            </div>

        </main>
    </section>
</body>

</html>