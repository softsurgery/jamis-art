<?php

$uploadGroupsMap = [
    0 => 'root',
    1 => 'art-type',
    2 => 'articles',
    3 => 'resources',
    4 => 'profiles'
];
$uploadGroupsMapReverse = array_flip($uploadGroupsMap);

function getGroupIdByName($name)
{
    global $uploadGroupsMapReverse;
    return $uploadGroupsMapReverse[$name];
}

function getGroupNameById($id)
{
    global $uploadGroupsMap;
    return $uploadGroupsMap[$id];
}