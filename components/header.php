<?php

function headerLayout()
{
    return '
    <div class="flex items-center justify-between w-full h-8">
        <h1 class="text-xl font-bold text-gray-800">Welcome, Admin!</h1>
        <div class="flex items-center gap-4">
            <div class="relative">
                <input type="text" placeholder="Search..." class="rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm outline-none" />
            </div>
            <div class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 p-2 rounded-lg transition">
                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">A</div>
                <span class="text-sm font-medium text-gray-700 hidden sm:block">Admin User</span>
            </div>
        </div>
    </div>
    ';
}
?>