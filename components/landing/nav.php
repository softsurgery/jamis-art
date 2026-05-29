<?php

function landingNavLayout($name, $isLoggedIn = false, $path = "")
{
    $items = array(
        [
            'label' => 'Home',
            'name' => 'home',
            'route' => ''
        ],
        [
            'label' => 'Gallery',
            'name' => 'gallery',
            'route' => '/views/landing/gallery.php'
        ],
    );

    $navItems = '';
    foreach ($items as $item) {
        $activeClass = $item['name'] === $name ? 'text-red-500' : 'hover:text-red-500 transition';
        $navItems .= "<a href='$path" . $item['route'] . "' class='$activeClass'>" . $item['label'] . "</a>";
    }

    $authBlock = '';
    if ($isLoggedIn) {
        $authBlock = "
        <a href='$path/views/auth/sign-out.php' class='bg-red-600 hover:bg-red-700 px-5 py-2 rounded-full font-semibold transition'>
            Sign Out
        </a>";
    } else {
        $authBlock = " 
        <button class='bg-red-600 hover:bg-red-700 px-5 py-2 rounded-full font-semibold transition' onclick='window.location.href=\"$path/views/auth/sign-in.php\"'>
            Join Now
        </button>";
    }



    return " 
    <div class='max-w-7xl mx-auto px-6 py-4 flex items-center justify-between'>
        <div class='flex items-center gap-3'>
            <div class='w-10 h-10 rounded-full bg-red-600'></div>
                <h1 class='text-2xl title-font tracking-wider'>
                    ART<span class='gradient-text'>VERSE</span>
                </h1>
            </div>

            <div class='hidden md:flex items-center gap-10 text-sm uppercase tracking-widest'>
                $navItems
            </div>
            <div>
                $authBlock  
            </div>
        </div>
    </div>
    ";
}