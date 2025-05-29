<?php

namespace App\Http\Requests;

use App\Rules\SQLInjectionValidate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{   
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
                new SQLInjectionValidate
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:customers,email',
                new SQLInjectionValidate,
            ],
            'phone' => [
                'required',
                'string',              
                'regex:/^[0-9]{10}$/',
                new SQLInjectionValidate,
            ],
             'address' => [
                'nullable',
                'string',
                'max:255',
                new SQLInjectionValidate,
            ],
            
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.regex' => 'Tên chỉ được chứa chữ cái, ký tự có dấu và khoảng trắng.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số.',         
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ];
    }
}
