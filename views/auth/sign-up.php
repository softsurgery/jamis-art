<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../../assets/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="text-white min-h-screen flex items-center justify-center px-6 bg-art">

    <div class="grid lg:grid-cols-2 max-w-6xl w-full rounded-[40px] overflow-hidden glass">

        <!-- LEFT FORM -->
        <div class="p-8 md:p-14">

            <p class="uppercase tracking-[6px] text-red-500 text-sm mb-3">
                Join The Movement
            </p>

            <h2 class="text-5xl title-font mb-10">
                CREATE <span class="gradient-text">ACCOUNT</span>
            </h2>

            <form class="space-y-5">

                <div class="flex flex-row gap-4">
                    <div>
                        <label class="text-sm text-gray-300 mb-2 block">
                            First Name
                        </label>

                        <input type="text" placeholder="Your first name"
                            class="input-style w-full rounded-2xl px-5 py-4" />
                    </div>

                    <div>
                        <label class="text-sm text-gray-300 mb-2 block">
                            Last Name
                        </label>

                        <input type="text" placeholder="Your last name"
                            class="input-style w-full rounded-2xl px-5 py-4" />
                    </div>
                </div>


                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Email Address
                    </label>

                    <input type="email" placeholder="you@example.com"
                        class="input-style w-full rounded-2xl px-5 py-4" />
                </div>

                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Password
                    </label>

                    <input type="password" placeholder="••••••••" class="input-style w-full rounded-2xl px-5 py-4" />
                </div>

                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Confirm Password
                    </label>

                    <input type="password" placeholder="••••••••" class="input-style w-full rounded-2xl px-5 py-4" />
                </div>

                <div>
                    <label class="text-sm text-gray-300 mb-2 block">
                        Choose Your Art
                    </label>

                    <select class="input-style w-full rounded-2xl px-5 py-4">
                        <option class="bg-black">Painting</option>
                        <option class="bg-black">Dancing</option>
                        <option class="bg-black">Music</option>
                        <option class="bg-black">Acting</option>
                    </select>
                </div>

                <button class="w-full bg-red-600 hover:bg-red-700 transition py-4 rounded-2xl font-semibold text-lg">
                    Create Account
                </button>

            </form>

            <p class="text-center text-gray-400 mt-8">
                Already have an account?
                <a href="sign-in.php" class="text-red-400 hover:text-red-300">
                    Sign In
                </a>
            </p>

        </div>

        <!-- RIGHT -->
        <div class="hidden lg:flex flex-col justify-center p-14 border-l border-white/10">

            <div>
                <h1 class="text-6xl title-font leading-none mb-6">
                    CREATE.
                    <span class="gradient-text">EXPRESS.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-10">
                    Your art has a place here.
                    Join creators from all disciplines and
                    build your artistic identity.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-5">

                <div class="glass p-6 rounded-3xl">
                    <div class="text-4xl mb-4">💃</div>
                    <h3 class="font-semibold text-xl mb-2">Dance</h3>
                    <p class="text-sm text-gray-400">
                        Workshops & battles
                    </p>
                </div>

                <div class="glass p-6 rounded-3xl">
                    <div class="text-4xl mb-4">🎵</div>
                    <h3 class="font-semibold text-xl mb-2">Music</h3>
                    <p class="text-sm text-gray-400">
                        Produce & perform
                    </p>
                </div>

                <div class="glass p-6 rounded-3xl">
                    <div class="text-4xl mb-4">🎨</div>
                    <h3 class="font-semibold text-xl mb-2">Painting</h3>
                    <p class="text-sm text-gray-400">
                        Digital & traditional
                    </p>
                </div>

                <div class="glass p-6 rounded-3xl">
                    <div class="text-4xl mb-4">🎭</div>
                    <h3 class="font-semibold text-xl mb-2">Acting</h3>
                    <p class="text-sm text-gray-400">
                        Theater & cinema
                    </p>
                </div>

            </div>

        </div>

    </div>

</body>

</html>