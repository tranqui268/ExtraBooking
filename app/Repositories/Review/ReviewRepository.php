<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Repositories\BaseRepository;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface{

    public function __construct(Review $review){
        parent::__construct($review);
    }

    public function filters($filters){

    }

    public function softDelete($id){
        
    }
}