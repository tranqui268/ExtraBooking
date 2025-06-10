<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_name' => [
                'string',
                'max:255',
                'required',
                'regex:/^[\p{L}\s]+$/u',
                new SQLInjectionValidate
            ],
            'base_price' => [
                'required',
                'integer',
                'min:1'
            ],
            'maintenance_interval' => [
                'required',
                'integer'
            ],
            'duration' => [
                'required',
                'integer',
                'min:1'
            ],
            'description' => [
                'nullable',
                new SQLInjectionValidate
            ]
        ];
    }

    public function messages()
    {
        return [
            'service_name.required' => 'Tên là bắt buộc.',
            'service_name.string' => 'Tên phải là chuỗi ký tự.',
            'service_name.max' => 'Tên không được vượt quá 255 ký tự.',
            'service_name.regex' => 'Tên chỉ được chứa chữ cái, ký tự có dấu và khoảng trắng.',
            'base_price.required' => 'Giá là bắt buộc',
            'base_price.integer' => 'Giá phải là số nguyên',
            'base_price.min' => 'Giá phải là số nguyên dương', 
            'maintenance_interval' => 'Thời gian bảo trì là bắt buộc',
            'maintenance_interval' => 'Thời gian bảo trì phải là số nguyên dương',
            'duration.required' => 'Giá là bắt buộc',
            'duration.integer' => 'Giá phải là số nguyên',
            'duration.min' => 'Giá phải là số nguyên dương'           
        ];
    }
}
