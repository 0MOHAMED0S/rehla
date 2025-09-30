<?php

namespace App\Http\Requests\Shipping;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRequest extends FormRequest
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
            'name'  => 'required|string|min:3|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string'   => 'الاسم يجب أن يكون نصًا.',
            'name.min'      => 'الاسم يجب أن يحتوي على 3 أحرف على الأقل.',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرفًا.',
            'name.unique'   => 'هذا الاسم مستخدم من قبل، يرجى اختيار اسم آخر.',

            'price.required' => 'حقل السعر مطلوب.',
            'price.numeric'  => 'السعر يجب أن يكون رقمًا.',
            'price.min'      => 'السعر يجب أن يكون أكبر من 0.',
        ];
    }
}
