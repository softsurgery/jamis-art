<?php

function navLayout($name, $path = "", $dark = false)
{
    $items = array(
        [
            "icon" => "house",
            "name" => "dashboard",
            "label" => "Dashboard",
            "route" => "views/admin/dashboard.php",
        ],
        [
            "icon" => "users",
            "name" => "users",
            "label" => "Users",
            "route" => "views/admin/users.php",
        ],
        [
            "icon" => "palette",
            "name" => "art-type",
            "label" => "Art Types",
            "route" => "views/admin/art-type.php",
        ],
        [
            "icon" => "map-pin",
            "name" => "locations",
            "label" => "Locations",
            "route" => "views/admin/locations.php",
        ],
        [
            "icon" => "newspaper",
            "name" => "articles",
            "label" => "Articles",
            "route" => "views/admin/article.php",
        ],
        [
            "icon" => "settings",
            "name" => "settings",
            "label" => "Settings",
            "route" => "views/admin/settings.php",
        ]
    );

    $navItems = "";
    foreach ($items as $item) {
        $activeClass = $item["name"] === $name ? "bg-gray-100 text-indigo-600" : "text-gray-700 hover:bg-gray-100 hover:text-indigo-600";
        $navItems .= '
        <li>
            <a href="' . $path . $item["route"] . '" class="flex items-center gap-3 rounded-lg px-3 py-2 transition ' . $activeClass . '">
                <i data-lucide="' . $item["icon"] . '"></i>
                <span class="font-medium text-sm">' . $item["label"] . '</span>
            </a>
        </li>';
    }

    return " <div class='flex flex-col h-full space-y-2'>
       <img src='$path/" . ($dark ? 'assets/img/jemis-art-dark.png' : 'assets/img/jemis-art.png') . "' alt='Logo' class='w-32 h-auto mx-auto'>
        <ul class='flex flex-col space-y-1 flex-1'>
            ' . $navItems . '
        </ul>
        <div class='mt-auto pt-4 border-t border-gray-200'>
             <a href='#' class='flex items-center gap-3 rounded-lg px-3 py-2 text-red-600 transition hover:bg-red-50'>
                <svg class='w-5 h-5 opacity-75' fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'></path></svg>
                <span class='font-medium text-sm'>Sign Out</span>
            </a>
        </div>
    </div>";
}