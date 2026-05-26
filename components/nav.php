<?php

function navLayout()
{
    $items = array(
        [
            "icon" => "home",
            "label" => "Dashboard",
            "active" => false
        ],
        [
            "icon" => "users",
            "label" => "Users",
            "active" => true
        ],
        [
            "icon" => "art",
            "label" => "Art Types",
            "active" => false
        ],
        [
            "icon" => "settings",
            "label" => "Settings",
            "active" => false
        ]
    );

    $navItems = "";
    foreach ($items as $item) {
        $activeClass = $item["active"] ? "bg-gray-100 text-indigo-600" : "text-gray-700 hover:bg-gray-100 hover:text-indigo-600";
        $navItems .= '
        <li>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 transition ' . $activeClass . '">
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium text-sm">' . $item["label"] . '</span>
            </a>
        </li>';
    }

    return '
    <div class="flex flex-col h-full space-y-2">
        <div class="mb-6 px-2">
            <span class="text-2xl font-black tracking-wider text-indigo-600 font-[Anton]">JAMIS ART</span>
        </div>
        <ul class="flex flex-col space-y-1 flex-1">
            ' . $navItems . '
        </ul>
        <div class="mt-auto pt-4 border-t border-gray-200">
             <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-red-600 transition hover:bg-red-50">
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="font-medium text-sm">Sign Out</span>
            </a>
        </div>
    </div>
    ';
}