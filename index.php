<?php
session_start();
// require_once "connect.php";
require_once __DIR__ . "/controllers/AuthController.php";

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JemisArt - Welcome</title>
    <link rel="stylesheet" href="assets/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="text-white">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 w-full z-50 glass">
        <?php
        require_once __DIR__ . '/components/landing/nav.php';
        echo landingNavLayout('home', $isLoggedIn, '.');
        ?>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-bg min-h-screen flex items-center py-20 lg:py-0">

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center w-full">

            <!-- LEFT -->
            <div>
                <p class="uppercase tracking-[8px] text-red-500 mb-4">
                    Creative Community
                </p>

                <h1 class="text-6xl md:text-8xl leading-none title-font mb-6">
                    FECH
                    <span class="gradient-text">T9AWEM</span> ?
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed max-w-xl mb-8">
                    Art is how we resist.
                    Discover your creative identity through dance, music,
                    acting and painting.
                </p>

                <div class="flex gap-4">
                    <button
                        class="bg-red-600 hover:bg-red-700 px-8 py-4 rounded-full font-semibold transition cursor-pointer">
                        Enter The House
                    </button>

                    <button
                        class="border border-white/30 hover:bg-white hover:text-black px-8 py-4 rounded-full font-semibold transition cursor-pointer">
                        Explore Artists
                    </button>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="relative flex justify-center items-center mt-12 lg:mt-0">

                <div class="absolute w-72 h-72 bg-red-600 rounded-full blur-[150px] opacity-30 pointer-events-none">
                </div>

                <!-- Orbiting Animation Container -->
                <div class="orbit-container">
                    <!-- Ambient 3D Glowing Rings (No static resist text) -->
                    <div class="orbit-glow-ring ring-1"></div>
                    <div class="orbit-glow-ring ring-2"></div>
                    <div class="orbit-glow-ring ring-3"></div>

                    <!-- Orbiting Eyes with color-coded glows -->
                    <div class="orbit-eye-wrapper" style="--delay: 0s; --glow-color: #ff5e3a;">
                        <img src="assets/img/eyes/eye1.png" alt="Creative Vision 1" class="orbit-eye" />
                    </div>
                    <div class="orbit-eye-wrapper" style="--delay: -4s; --glow-color: #00bfff;">
                        <img src="assets/img/eyes/eye2.png" alt="Creative Vision 2" class="orbit-eye" />
                    </div>
                    <div class="orbit-eye-wrapper" style="--delay: -8s; --glow-color: #d63384;">
                        <img src="assets/img/eyes/eye3.png" alt="Creative Vision 3" class="orbit-eye" />
                    </div>
                    <div class="orbit-eye-wrapper" style="--delay: -12s; --glow-color: #fd7e14;">
                        <img src="assets/img/eyes/eye4.png" alt="Creative Vision 4" class="orbit-eye" />
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- ART TYPES -->
    <section class="py-28 px-6">

        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-20">
                <p class="uppercase tracking-[6px] text-red-500 mb-4">
                    Explore
                </p>

                <h2 class="text-5xl md:text-6xl title-font">
                    ART <span class="gradient-text">TYPES</span>
                </h2>
            </div>

            <!-- CARDS -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- PAINTING -->
                <div class="flip-card">
                    <div class="flip-inner">

                        <div class="flip-front glass p-6 flex flex-col justify-end"
                            style="background:url('https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=900&auto=format&fit=crop') center/cover;">
                            <div class="bg-black/60 p-5 rounded-2xl">
                                <h3 class="text-3xl title-font">PAINTING</h3>
                                <p class="text-gray-300 mt-2">Create. Imagine. Express.</p>
                            </div>
                        </div>

                        <div
                            class="flip-back bg-gradient-to-br from-red-600 to-black p-8 flex flex-col justify-center items-center text-center">
                            <h3 class="text-3xl title-font mb-4">PAINTING</h3>
                            <p class="text-gray-200">
                                Learn color theory, digital art,
                                graffiti, illustration and more.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- DANCING -->
                <div class="flip-card">
                    <div class="flip-inner">

                        <div class="flip-front glass p-6 flex flex-col justify-end"
                            style="background:url('https://images.unsplash.com/photo-1508804185872-d7badad00f7d?q=80&w=900&auto=format&fit=crop') center/cover;">
                            <div class="bg-black/60 p-5 rounded-2xl">
                                <h3 class="text-3xl title-font">DANCING</h3>
                                <p class="text-gray-300 mt-2">Move. Feel. Perform.</p>
                            </div>
                        </div>

                        <div
                            class="flip-back bg-gradient-to-br from-orange-500 to-black p-8 flex flex-col justify-center items-center text-center">
                            <h3 class="text-3xl title-font mb-4">DANCING</h3>
                            <p class="text-gray-200">
                                Hip-hop, freestyle, street dance,
                                choreography and workshops.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- MUSIC -->
                <div class="flip-card">
                    <div class="flip-inner">

                        <div class="flip-front glass p-6 flex flex-col justify-end"
                            style="background:url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=900&auto=format&fit=crop') center/cover;">
                            <div class="bg-black/60 p-5 rounded-2xl">
                                <h3 class="text-3xl title-font">MUSIC</h3>
                                <p class="text-gray-300 mt-2">Sing. Produce. Create.</p>
                            </div>
                        </div>

                        <div
                            class="flip-back bg-gradient-to-br from-pink-600 to-black p-8 flex flex-col justify-center items-center text-center">
                            <h3 class="text-3xl title-font mb-4">MUSIC</h3>
                            <p class="text-gray-200">
                                Explore vocals, beats,
                                recording and live performances.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- ACTING -->
                <div class="flip-card">
                    <div class="flip-inner">

                        <div class="flip-front glass p-6 flex flex-col justify-end"
                            style="background:url('https://images.unsplash.com/photo-1503095396549-807759245b35?q=80&w=900&auto=format&fit=crop') center/cover;">
                            <div class="bg-black/60 p-5 rounded-2xl">
                                <h3 class="text-3xl title-font">ACTING</h3>
                                <p class="text-gray-300 mt-2">Perform. Emote. Inspire.</p>
                            </div>
                        </div>

                        <div
                            class="flip-back bg-gradient-to-br from-purple-600 to-black p-8 flex flex-col justify-center items-center text-center">
                            <h3 class="text-3xl title-font mb-4">ACTING</h3>
                            <p class="text-gray-200">
                                Theater, cinema, improv,
                                storytelling and stage presence.
                            </p>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-white/10 py-8 text-center text-gray-400">
        © <?php
        echo date("Y");
        ?> ArtVerse — Create. Resist. Express.
    </footer>
    <script>
        function redirectToSignIn() {
            window.location.href = 'views/auth/sign-in.php';
        }
    </script>
</body>

</html>