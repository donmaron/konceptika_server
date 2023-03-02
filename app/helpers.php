<?php

function generateUuid(int $length = 32): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $uuid = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length/2; $i++) {
        $uuid .= $characters[random_int(0, $max)];
    }
    $uuid .= '-';
    for ($i = 0; $i < $length/4; $i++) {
        $uuid .= $characters[random_int(0, $max)];
    }
    $uuid .= '-';
    for ($i = 0; $i < $length/4; $i++) {
        $uuid .= $characters[random_int(0, $max)];
    }
    return $uuid;
}
