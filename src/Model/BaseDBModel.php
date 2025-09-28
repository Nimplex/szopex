<?php

namespace App\Model;

use PDO;

class BaseDBModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
}
