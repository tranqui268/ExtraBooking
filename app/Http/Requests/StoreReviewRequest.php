<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required',
            'repair_order_id' => 'required|exists:repair_orders,id',
            'comment' => 'required',
            'response' => 'required'
        ];
    }

    public function messages(){
        return [
            'rating.required' => 'Số sao đánh giá là bắt buộc',
            'repair_order_id' => 'Vui lòng chọn đúng lịch sử sửa chữa',
            'repair_order_id.exists' => 'Đơn sửa chữa không tồn tại',
            'comment.required' => 'Bình luận là bắt buộc'
        ];
    }
}
