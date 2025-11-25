<?php

global $_ROUTE;

[ 'user_id' => $user_id ] = $_SESSION;
[ 'id' => $destination ] = $_ROUTE;
[ 'listing_id' => $listing_id ] = $_GET;

$chat = (new App\Builder\ChatsBuilder())->make();

$already_existing = $chat->find_standalone($destination, $user_id);

if ($already_existing) {
    header("Location: /messages/{$already_existing['id']}", 303, true);
    die;
}

$res = $chat->create($destination, $user_id);

if (!$res) {
    // @todo: propper handling
    // throw \Exception('Database error', 1);
}


header("Location: /messages/{$res}", 303, true);
