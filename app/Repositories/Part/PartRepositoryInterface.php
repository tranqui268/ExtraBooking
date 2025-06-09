<?php

namespace App\Repositories\Part;

use App\Repositories\RepositoryInterface;

interface PartRepositoryInterface extends RepositoryInterface{
    public function updatePartsStock(array $partIds, array $quantities);
}