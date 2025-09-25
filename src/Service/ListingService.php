<?php

namespace App\Service;

use App\Model\Listing;

class ListingService
{
    private Listing $listingModel;

    public function __construct(Listing $listingModel)
    {
        $this->listingModel = $listingModel;
    }

    public function listAll(int $page): ?array
    {
        $page = max(1, min(1000, $page));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        return $this->listingModel->listAll($perPage, $offset);
    }
}
