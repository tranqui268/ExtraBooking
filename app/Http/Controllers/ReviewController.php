<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Repositories\Review\ReviewRepositoryInterface;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewRepo;

    public function __construct(ReviewRepositoryInterface $reviewRepo){
        $this->reviewRepo = $reviewRepo;
    }
    
    public function create(StoreReviewRequest $request){
        try {
            $review = $this->reviewRepo->create($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Tạo đánh giá thành công',
                'data' => $review
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Thêm đánh giá thất bại: ' . $e->getMessage()
            ], 400);
        }

    }
}
