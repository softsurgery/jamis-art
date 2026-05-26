<?php

require_once __DIR__ . "/../controllers/UserController.php";


function userTableLayout()
{
    $userController = new UserController();

    $columns = array(
        'ID',
        'Name',
        'Email',
        'Role',
        'Status',
    );

    $columnsBlock = '';
    foreach ($columns as $column) {
        $columnsBlock .= "<th class='whitespace-nowrap px-4 py-3 text-gray-900'> $column </th>";
    }

    $users = $userController->getAll();

    $usersBlock = "";

    foreach ($users as $user) {
        $usersBlock .= "
        <tr class='hover:bg-gray-50'>
            <td class='whitespace-nowrap px-4 py-3 font-medium text-gray-900'>{$user['id']}</td>
            <td class='whitespace-nowrap px-4 py-3 text-gray-700'>{$user['firstName']} {$user['lastName']}</td>
            <td class='whitespace-nowrap px-4 py-3 text-gray-700'>{$user['email']}</td>
            <td class='whitespace-nowrap px-4 py-3 text-gray-700'>{$user['artTypeId']}</td>
            <td class='whitespace-nowrap px-4 py-3'>
                <span class='inline-flex items-center justify-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs text-emerald-700'>Active</span>
            </td>
        </tr>
    ";
    }

    return "
    <div class='overflow-x-auto rounded-lg border border-gray-200 shadow-sm mt-4'>
        <table class='min-w-full divide-y-2 divide-gray-200 bg-white text-sm'>
            <thead class='bg-gray-50 font-semibold text-left'>
                <tr>
                    $columnsBlock
                    <th class='whitespace-nowrap px-4 py-3 text-gray-900 text-center'>Actions</th>
                </tr>
            </thead>
            <tbody class='divide-y divide-gray-200 bg-white'>

               $usersBlock
                
            </tbody>
        </table>
    </div>";
}
