<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'service_id' => 'required|string|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'Vui lòng có thông tin khách hàng',
            'service_id.required' => 'Vui lòng chọn dịch vụ',
            'service_id.exists' => 'Dịch vụ không tồn tại',
            'appointment_date.required' => 'Vui lòng chọn ngày hẹn',
            'start_time.required' => 'Vui lòng chọn giờ bắt đầu',
            'start_time.date_format' => 'Định dạng giờ không hợp lệ'
        ];
    }
}
