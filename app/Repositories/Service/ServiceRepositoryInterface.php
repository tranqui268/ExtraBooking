<?php

namespace App\Repositories\Service;

use App\Repositories\RepositoryInterface;

interface ServiceRepositoryInterface extends RepositoryInterface{
    public function generateId($serviceName);

}