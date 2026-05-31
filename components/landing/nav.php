<?php

function landingNavLayout($name, $isLoggedIn = false, $path = "", $dark = true)
{
    $items = array(
        [
            'label' => 'Home',
            'name' => 'home',
            'route' => '/'
        ],
        [
            'label' => 'Gallery',
            'name' => 'gallery',
            'route' => '/views/landing/gallery.php'
        ],
        [
            'label' => 'Maps',
            'name' => 'maps',
            'route' => '/views/landing/map.php'
        ]
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
            <div class='w-10 h-10 rounded-full'></div>
                <img src='$path/" . ($dark ? 'assets/img/jemis-art-dark.png' : 'assets/img/jemis-art.png') . "' alt='Logo' class='w-32 h-auto'>
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