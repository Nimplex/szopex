<?php

namespace App\Builder;

use App\Model\Chat;

class ChatsBuilder
{
    public function make(): Chat
    {
        require __DIR__ . '/../../bootstrap.php';

        /** @var PDO $db */
        $chat = new Chat($db);
        return $chat;
    }
}
