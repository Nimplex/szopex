<?php

namespace App\Builder;

use App\Model\Auth;

class AuthBuilder
{
    public function make(): Auth
    {
        require __DIR__ . '/../../bootstrap.php';

        /** @var PDO $db */
        $user = new Auth($db);
        return $user;
    }
}
