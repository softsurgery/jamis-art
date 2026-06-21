<?php
session_start();
// require_once "connect.php";
require_once __DIR__ . "/controllers/AuthController.php";
require_once __DIR__ . "/controllers/PartnerController.php";
require_once __DIR__ . "/controllers/EventController.php";
require_once __DIR__ . "/controllers/ArtTypeController.php";

$isLoggedIn = isset($_SESSION['user_id']);
$partnerController = new PartnerController();
$partners = $partnerController->getAll();

$eventController = new EventController();
$events = $eventController->getAll();
$artTypeController = new ArtTypeController();
$artTypes = $artTypeController->getAll();
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
    <section class="hero-bg min-h-screen flex items-center py-20 lg:py-0 relative overflow-hidden">

        <!-- Orbiting Animation Container (Hero Wide) -->
        <div class="orbit-container">
            <!-- Ambient 3D Glowing Rings (No static resist text) -->
            <div class="orbit-glow-ring ring-1"></div>
            <div class="orbit-glow-ring ring-2"></div>
            <div class="orbit-glow-ring ring-3"></div>

            <!-- Orbiting Eyes with color-coded glows and distinct paths -->
            <!-- Original 4 -->
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-1; animation-duration: 18s; animation-delay: 0s; --glow-color: #ff5e3a;">
                <img src="assets/img/eyes/eye1.png" alt="Creative Vision 1" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-2; animation-duration: 22s; animation-delay: -5s; --glow-color: #00bfff;">
                <img src="assets/img/eyes/eye2.png" alt="Creative Vision 2" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-3; animation-duration: 25s; animation-delay: -12s; --glow-color: #d63384;">
                <img src="assets/img/eyes/eye3.png" alt="Creative Vision 3" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-4; animation-duration: 20s; animation-delay: -8s; --glow-color: #fd7e14;">
                <img src="assets/img/eyes/eye4.png" alt="Creative Vision 4" class="orbit-eye" />
            </div>

            <!-- Duplicated 4 (Swarming) -->
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-2; animation-duration: 28s; animation-delay: -15s; animation-direction: reverse; --glow-color: #ff5e3a;">
                <img src="assets/img/eyes/eye1.png" alt="Creative Vision 1" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-3; animation-duration: 19s; animation-delay: -2s; animation-direction: reverse; --glow-color: #00bfff;">
                <img src="assets/img/eyes/eye2.png" alt="Creative Vision 2" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-4; animation-duration: 24s; animation-delay: -18s; animation-direction: reverse; --glow-color: #d63384;">
                <img src="assets/img/eyes/eye3.png" alt="Creative Vision 3" class="orbit-eye" />
            </div>
            <div class="orbit-eye-wrapper" style="animation-name: orbit-path-1; animation-duration: 26s; animation-delay: -10s; animation-direction: reverse; --glow-color: #fd7e14;">
                <img src="assets/img/eyes/eye4.png" alt="Creative Vision 4" class="orbit-eye" />
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center w-full relative z-10 pointer-events-none">

            <!-- LEFT -->
            <div class="pointer-events-auto">
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
            <div class="relative flex justify-center items-center mt-12 lg:mt-0 pointer-events-none">
                <div class="absolute w-72 h-72 bg-red-600 rounded-full blur-[150px] opacity-30 pointer-events-none">
                </div>
            </div>

        </div>
    </section>

    <!-- ABOUT US (OUR MANIFESTO) -->
    <section id="about-us" class="py-28 px-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-600 rounded-full blur-[180px] opacity-10 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- LEFT: Image with Neon Glow Frame -->
            <div class="relative group">
                <div class="absolute -inset-1.5 bg-gradient-to-r from-red-600 to-pink-600 rounded-2xl blur-lg opacity-40 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative bg-black rounded-2xl overflow-hidden border border-white/10">
                    <img src="assets/img/about-manifesto.png" alt="Manifesto of Resistance" class="w-full h-[450px] object-cover scale-100 group-hover:scale-105 transition-transform duration-700" />
                </div>
            </div>

            <!-- RIGHT: Manifesto Narrative -->
            <div>
                <p class="uppercase tracking-[8px] text-red-500 font-semibold mb-4">
                    Who We Are
                </p>
                <h2 class="text-5xl md:text-6xl title-font mb-8">
                    THE ART OF <span class="gradient-text">RESISTANCE</span>
                </h2>
                <p class="text-gray-300 text-lg leading-relaxed mb-6">
                    JemisArt is more than an art space; it is a movement born out of the necessity to express, challenge, and break boundaries. In a world that constantly demands conformity, creative expression becomes our most potent form of defiance.
                </p>
                <p class="text-gray-400 text-base leading-relaxed mb-10">
                    Whether it is the stroke of a brush, a physical movement, the resonance of a chord, or a theatrical voice—every piece of art is a statement. We provide the tools, the sanctuary, and the canvas for creators to stand tall and speak their truths.
                </p>

                <!-- Stat Cards -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="glass p-5 rounded-2xl text-center stat-card cursor-pointer">
                        <div class="text-3xl md:text-4xl font-bold title-font text-red-500 mb-1">120+</div>
                        <div class="text-xs uppercase tracking-widest text-gray-400">Creators</div>
                    </div>
                    <div class="glass p-5 rounded-2xl text-center stat-card cursor-pointer">
                        <div class="text-3xl md:text-4xl font-bold title-font text-red-500 mb-1">4</div>
                        <div class="text-xs uppercase tracking-widest text-gray-400">Disciplines</div>
                    </div>
                    <div class="glass p-5 rounded-2xl text-center stat-card cursor-pointer">
                        <div class="text-3xl md:text-4xl font-bold title-font text-red-500 mb-1">25+</div>
                        <div class="text-xs uppercase tracking-widest text-gray-400">Activations</div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- HOW WE RESIST (THE JOURNEY) -->
    <section id="journey" class="py-28 px-6 bg-gradient-to-b from-transparent via-white/[0.02] to-transparent">
        <div class="max-w-7xl mx-auto">
            
            <div class="text-center mb-20">
                <p class="uppercase tracking-[6px] text-red-500 mb-4">
                    The Process
                </p>
                <h2 class="text-5xl md:text-6xl title-font">
                    HOW WE <span class="gradient-text">RESIST</span>
                </h2>
            </div>

            <!-- 4 Step Cards -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                <!-- Step 01 -->
                <div class="glass p-8 rounded-2xl journey-card flex flex-col justify-between h-[280px] cursor-pointer" style="--journey-glow: #ff5e3a;">
                    <div>
                        <div class="text-5xl font-bold title-font text-white/10 journey-number transition mb-6">01</div>
                        <h3 class="text-2xl font-bold mb-3">Discover</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Explore painting, dancing, acting, or music to discover your unique artistic medium of expression.
                        </p>
                    </div>
                    <div class="h-1 w-12 bg-[#ff5e3a] rounded"></div>
                </div>

                <!-- Step 02 -->
                <div class="glass p-8 rounded-2xl journey-card flex flex-col justify-between h-[280px] cursor-pointer" style="--journey-glow: #00bfff;">
                    <div>
                        <div class="text-5xl font-bold title-font text-white/10 journey-number transition mb-6">02</div>
                        <h3 class="text-2xl font-bold mb-3">Unite</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Join a workspace and connect with fellow creators, local mentors, and supportive art collectives.
                        </p>
                    </div>
                    <div class="h-1 w-12 bg-[#00bfff] rounded"></div>
                </div>

                <!-- Step 03 -->
                <div class="glass p-8 rounded-2xl journey-card flex flex-col justify-between h-[280px] cursor-pointer" style="--journey-glow: #d63384;">
                    <div>
                        <div class="text-5xl font-bold title-font text-white/10 journey-number transition mb-6">03</div>
                        <h3 class="text-2xl font-bold mb-3">Ignite</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Collaborate, experiment, and fuel the fire of creation, pushing the boundaries of traditional art forms.
                        </p>
                    </div>
                    <div class="h-1 w-12 bg-[#d63384] rounded"></div>
                </div>

                <!-- Step 04 -->
                <div class="glass p-8 rounded-2xl journey-card flex flex-col justify-between h-[280px] cursor-pointer" style="--journey-glow: #fd7e14;">
                    <div>
                        <div class="text-5xl font-bold title-font text-white/10 journey-number transition mb-6">04</div>
                        <h3 class="text-2xl font-bold mb-3">Disrupt</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Showcase your creations on our digital maps, gallery exhibitions, and community activations.
                        </p>
                    </div>
                    <div class="h-1 w-12 bg-[#fd7e14] rounded"></div>
                </div>

            </div>

        </div>
    </section>

    <!-- EVENTS SECTION -->
    <section id="events" class="py-28 px-6 relative overflow-hidden bg-black/50">
        <div class="absolute top-0 left-0 w-96 h-96 bg-purple-600 rounded-full blur-[180px] opacity-10 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <p class="uppercase tracking-[6px] text-red-500 mb-3">
                    Upcoming Actions
                </p>
                <h3 class="text-4xl md:text-5xl title-font">
                    COMMUNITY <span class="gradient-text">EVENTS</span>
                </h3>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($events)): ?>
                    <div class="col-span-full text-center text-gray-500 py-12">
                        No events scheduled at the moment. Check back soon.
                    </div>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <?php 
                            $coverUrl = !empty($event['coverPath']) ? $event['coverPath'] : 'assets/img/placeholder.jpg'; 
                            $artType = array_filter($artTypes, fn($a) => $a['id'] == $event['artTypeId']);
                            $artType = reset($artType);
                            $colorValue = $artType ? $artType['colorValue'] : '#ffffff';
                            $label = $artType ? $artType['label'] : 'General';
                        ?>
                        <div class="glass rounded-2xl overflow-hidden group cursor-pointer border border-white/5 hover:border-[<?= $colorValue ?>]/30 transition-all duration-500 flex flex-col h-full" style="--event-glow: <?= $colorValue ?>;">
                            <div class="h-48 overflow-hidden relative">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                                <img src="<?= htmlspecialchars($coverUrl) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                <span class="absolute top-4 right-4 z-20 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full backdrop-blur-md bg-white/10 text-white border border-white/20" style="color: <?= $colorValue ?>;">
                                    <?= htmlspecialchars($label) ?>
                                </span>
                            </div>
                            <div class="p-6 flex flex-col flex-1 relative">
                                <div class="absolute top-0 left-6 right-6 h-[1px] bg-gradient-to-r from-transparent via-[<?= $colorValue ?>]/50 to-transparent"></div>
                                <h4 class="text-xl font-bold mb-3 text-white transition-colors" style="--tw-text-opacity: 1; color: var(--tw-text-opacity) == 1 ? currentColor : currentColor; " onmouseover="this.style.color='<?= $colorValue ?>'" onmouseout="this.style.color=''">
                                    <?= htmlspecialchars($event['title']) ?>
                                </h4>
                                <p class="text-gray-400 text-sm leading-relaxed mb-6 flex-1"><?= htmlspecialchars($event['description']) ?></p>
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-4 border-t border-white/5">
                                    <span><?= htmlspecialchars(date('M d, Y', strtotime($event['createdAt']))) ?></span>
                                    <a href="views/landing/event-details.php?id=<?= $event['id'] ?>" class="flex items-center gap-1 hover:text-white transition-colors">Details <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- PARTNERS & ALLIES -->
    <section id="partners" class="py-24 px-6 relative">
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-pink-600 rounded-full blur-[150px] opacity-10 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto">
            
            <div class="text-center mb-16">
                <p class="uppercase tracking-[6px] text-red-500 mb-3">
                    Co-conspirators
                </p>
                <h3 class="text-3xl md:text-4xl title-font">
                    PARTNERS & <span class="gradient-text">ALLIES</span>
                </h3>
            </div>

            <!-- Partner Logos Carousel -->
            <div class="relative w-full overflow-hidden group py-4 flex">
                <?php 
                $displayPartners = $partners;
                // Ensure there are enough items to fill the screen width for seamless looping
                if (!empty($partners)) {
                    while (count($displayPartners) < 8) {
                        $displayPartners = array_merge($displayPartners, $partners);
                    }
                }
                ?>
                
                <?php if (empty($partners)): ?>
                    <p class="text-gray-500 w-full text-center">More partners joining soon.</p>
                <?php else: ?>
                    <!-- Track 1 -->
                    <div class="flex animate-marquee w-max gap-8 pr-8">
                        <?php foreach ($displayPartners as $index => $partner): ?>
                        <?php 
                            $logoUrl = $partner->getLogoPath() ? $partner->getLogoPath() : ''; 
                            $glowColors = ['rgba(255, 94, 58, 0.3)', 'rgba(0, 191, 255, 0.3)', 'rgba(214, 51, 132, 0.3)', 'rgba(253, 126, 20, 0.3)'];
                            $color = $glowColors[$index % count($glowColors)];
                        ?>
                        <div class="shrink-0 w-[280px] border border-white/5 rounded-2xl p-8 flex items-center justify-center partner-card cursor-pointer h-32" style="--partner-glow: <?= $color ?>; --partner-glow-shadow: <?= str_replace('0.3', '0.15', $color) ?>;">
                            <?php if ($logoUrl): ?>
                                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($partner->getLabel()) ?>" class="max-h-full max-w-full object-contain filter grayscale hover:grayscale-0 transition duration-500" />
                            <?php else: ?>
                                <span class="font-['Anton'] text-2xl tracking-widest text-white uppercase text-center"><?= htmlspecialchars($partner->getLabel()) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Track 2 (Duplicate for seamless loop) -->
                    <div class="flex animate-marquee w-max gap-8 pr-8" aria-hidden="true">
                        <?php foreach ($displayPartners as $index => $partner): ?>
                        <?php 
                            $logoUrl = $partner->getLogoPath() ? $partner->getLogoPath() : ''; 
                            $glowColors = ['rgba(255, 94, 58, 0.3)', 'rgba(0, 191, 255, 0.3)', 'rgba(214, 51, 132, 0.3)', 'rgba(253, 126, 20, 0.3)'];
                            $color = $glowColors[$index % count($glowColors)];
                        ?>
                        <div class="shrink-0 w-[280px] border border-white/5 rounded-2xl p-8 flex items-center justify-center partner-card cursor-pointer h-32" style="--partner-glow: <?= $color ?>; --partner-glow-shadow: <?= str_replace('0.3', '0.15', $color) ?>;">
                            <?php if ($logoUrl): ?>
                                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($partner->getLabel()) ?>" class="max-h-full max-w-full object-contain filter grayscale hover:grayscale-0 transition duration-500" />
                            <?php else: ?>
                                <span class="font-['Anton'] text-2xl tracking-widest text-white uppercase text-center"><?= htmlspecialchars($partner->getLabel()) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <style>
            .animate-marquee {
                animation: marquee 30s linear infinite;
            }
            .group:hover .animate-marquee {
                animation-play-state: paused;
            }
            @keyframes marquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-100%); }
            }
            </style>

        </div>
    </section>

    <!-- CALL TO ACTION (CTA) -->
    <section class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            
            <div class="cta-glass-card rounded-3xl p-10 md:p-16 border border-white/10 flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="relative z-10 max-w-xl text-center md:text-left">
                    <h2 class="text-4xl md:text-5xl title-font mb-4">
                        READY TO <span class="gradient-text">JOIN THE FORCE?</span>
                    </h2>
                    <p class="text-gray-300 text-base leading-relaxed">
                        Step inside the House of Resistance. Register now to share your location, showcase your creations, and collaborate with standard and admin creators.
                    </p>
                </div>
                <div class="relative z-10 shrink-0">
                    <button onclick="redirectToSignIn()" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-lg px-10 py-5 rounded-full transition shadow-lg shadow-red-600/20 hover:shadow-red-600/40 cursor-pointer">
                        Enter The House
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <?php
    require_once __DIR__ . '/components/landing/footer.php';
    echo landingFooterLayout('.');
    ?>
    <script>
        function redirectToSignIn() {
            window.location.href = 'views/auth/sign-in.php';
        }
    </script>
</body>

</html>