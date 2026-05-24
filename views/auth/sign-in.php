<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth = new AuthController();
    $user = $auth->login($email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user->getId();
        header('Location: ../../index.php');
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="text-white min-h-screen flex items-center justify-center px-6 bg-art">

    <div class="grid lg:grid-cols-2 max-w-6xl w-full rounded-[40px] overflow-hidden glass">

        <!-- LEFT -->
        <div class="hidden lg:flex flex-col justify-between p-14 border-r border-white/10">

            <div>
                <h1 class="text-5xl title-font leading-none">
                    ART<span class="gradient-text">VERSE</span>
                </h1>

                <p class="text-gray-300 mt-6 text-lg leading-relaxed max-w-md">
                    Enter the creative universe.
                    Connect with artists, dancers,
                    musicians and actors around the world.
                </p>
            </div>

            <div class="space-y-6">

                <div class="glass p-5 rounded-2xl">
                    <h3 class="font-semibold text-xl mb-2">🎭 Acting</h3>
                    <p class="text-gray-400 text-sm">
                        Perform and express emotions.
                    </p>
                </div>

                <div class="glass p-5 rounded-2xl">
                    <h3 class="font-semibold text-xl mb-2">🎨 Painting</h3>
                    <p class="text-gray-400 text-sm">
                        Create visual stories and identities.
                    </p>
                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="p-8 md:p-14">

            <p class="uppercase tracking-[6px] text-red-500 text-sm mb-3">
                Welcome Back
            </p>

            <h2 class="text-5xl title-font mb-10">
                SIGN <span class="gradient-text">IN</span>
            </h2>

            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded-xl mb-6">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="" method="POST">

                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Email Address
                    </label>

                    <input type="email" name="email" placeholder="you@example.com" required
                        class="input-style w-full rounded-2xl px-5 py-4" />
                </div>

                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Password
                    </label>

                    <input type="password" name="password" placeholder="••••••••" required
                        class="input-style w-full rounded-2xl px-5 py-4" />
                </div>

                <div class="flex items-center justify-between text-sm">

                    <label class="flex items-center gap-2 text-gray-400">
                        <input type="checkbox">
                        Remember me
                    </label>

                    <a href="#" class="text-red-400 hover:text-red-300">
                        Forgot Password?
                    </a>

                </div>

                <button class="w-full bg-red-600 hover:bg-red-700 transition py-4 rounded-2xl font-semibold text-lg">
                    Sign In
                </button>

            </form>

            <div class="my-8 flex items-center gap-4">
                <div class="flex-1 h-[1px] bg-white/10"></div>
                <span class="text-gray-500 text-sm">OR</span>
                <div class="flex-1 h-[1px] bg-white/10"></div>
            </div>

            <button class="w-full glass py-4 rounded-2xl hover:bg-white/10 transition">
                Continue with Google
            </button>

            <p class="text-center text-gray-400 mt-8">
                Don't have an account?
                <a href="sign-up.php" class="text-red-400 hover:text-red-300">
                    Sign Up
                </a>
            </p>

        </div>

    </div>

</body>

</html>