<?php

function randomColor()
{
    $color = "rgba(";
    for ($i = 0; $i < 3; $i++) {
        $color .= rand(0, 255) . ", ";
    }
    $color .= "0.5)";
    return $color;
}