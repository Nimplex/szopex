<?php

namespace App\Builder;

use App\Model\Listing;

class ListingBuilder
{
    public function make(): Listing
    {
        require __DIR__ . '/../../bootstrap.php';

        /** @var PDO $db */
        $listing = new Listing($db);
        return $listing;
    }
}
