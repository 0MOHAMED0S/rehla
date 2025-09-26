<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string', 'max:1000'],
            'features_text'         => ['nullable', 'string'],
            'electronic_copy_price' => ['nullable', 'integer', 'min:0', 'required_without:printed_copy_price'],
            'printed_copy_price'    => ['nullable', 'integer', 'min:0', 'required_without:electronic_copy_price'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المنتج مطلوب.',
            'name.string'   => 'اسم المنتج يجب أن يكون نصاً.',
            'name.max'      => 'اسم المنتج لا يجب أن يتجاوز 255 حرفاً.',

            'description.string' => 'الوصف يجب أن يكون نصاً.',
            'description.max'    => 'الوصف لا يجب أن يتجاوز 1000 حرف.',

            'features_text.string' => 'المميزات يجب أن تكون نصاً.',

            'electronic_copy_price.integer'          => 'سعر النسخة الإلكترونية يجب أن يكون رقماً صحيحاً.',
            'electronic_copy_price.min'              => 'سعر النسخة الإلكترونية لا يمكن أن يكون أقل من 0.',
            'electronic_copy_price.required_without' => 'يجب إدخال سعر النسخة الإلكترونية أو النسخة المطبوعة على الأقل.',

            'printed_copy_price.integer'          => 'سعر النسخة المطبوعة يجب أن يكون رقماً صحيحاً.',
            'printed_copy_price.min'              => 'سعر النسخة المطبوعة لا يمكن أن يكون أقل من 0.',
            'printed_copy_price.required_without' => 'يجب إدخال سعر النسخة المطبوعة أو النسخة الإلكترونية على الأقل.',
        ];
    }
}
