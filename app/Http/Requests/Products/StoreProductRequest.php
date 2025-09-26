<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:3', 'max:255'],
            'description'           => ['required', 'string', 'min:10', 'max:1000'],
            'features_text'         => ['required', 'string', 'min:5', 'max:2000'],

            'fixed_price'           => ['nullable', 'integer', 'min:0'],
            'electronic_copy_price' => ['nullable', 'integer', 'min:0'],
            'printed_copy_price'    => ['nullable', 'integer', 'min:0'],
            'offered_price'         => ['nullable', 'integer', 'min:0'],

            'image'                 => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fixed      = $this->input('fixed_price');
            $electronic = $this->input('electronic_copy_price');
            $printed    = $this->input('printed_copy_price');
            $offer      = $this->input('offered_price');

            if (!is_null($fixed)) {
                if (!is_null($electronic) || !is_null($printed) || !is_null($offer)) {
                    $validator->errors()->add(
                        'fixed_price',
                        'إذا اخترت السعر الثابت لا يمكن إدخال أسعار أخرى.'
                    );
                }
            }
            else {
                if (is_null($electronic) && is_null($printed) && is_null($offer)) {
                    $validator->errors()->add(
                        'price',
                        'يجب إدخال سعر النسخة الإلكترونية أو المطبوعه أو العرض على الأقل عند عدم وجود سعر ثابت.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المنتج مطلوب.',
            'name.string'   => 'اسم المنتج يجب أن يكون نصاً.',
            'name.min'      => 'اسم المنتج يجب أن يحتوي على 3 أحرف على الأقل.',
            'name.max'      => 'اسم المنتج لا يجب أن يتجاوز 255 حرفاً.',

            'description.required' => 'الوصف مطلوب.',
            'description.string'   => 'الوصف يجب أن يكون نصاً.',
            'description.min'      => 'الوصف يجب أن يحتوي على 10 أحرف على الأقل.',
            'description.max'      => 'الوصف لا يجب أن يتجاوز 1000 حرف.',

            'features_text.required' => 'المميزات مطلوبة.',
            'features_text.string'   => 'المميزات يجب أن تكون نصاً.',
            'features_text.min'      => 'المميزات يجب أن تحتوي على 5 أحرف على الأقل.',
            'features_text.max'      => 'المميزات لا يجب أن تتجاوز 2000 حرف.',

            'fixed_price.integer' => 'السعر الثابت يجب أن يكون رقماً صحيحاً.',
            'fixed_price.min'     => 'السعر الثابت لا يمكن أن يكون أقل من 0.',

            'electronic_copy_price.integer' => 'سعر النسخة الإلكترونية يجب أن يكون رقماً صحيحاً.',
            'electronic_copy_price.min'     => 'سعر النسخة الإلكترونية لا يمكن أن يكون أقل من 0.',

            'printed_copy_price.integer' => 'سعر النسخة المطبوعة يجب أن يكون رقماً صحيحاً.',
            'printed_copy_price.min'     => 'سعر النسخة المطبوعة لا يمكن أن يكون أقل من 0.',

            'offered_price.integer' => 'السعر المعروض يجب أن يكون رقماً صحيحاً.',
            'offered_price.min'     => 'السعر المعروض لا يمكن أن يكون أقل من 0.',

            'image.required' => 'الصورة مطلوبة.',
            'image.image'    => 'يجب أن يكون الملف صورة.',
            'image.mimes'    => 'يجب أن تكون الصورة بصيغة: jpeg, png, jpg, gif, webp.',
            'image.max'      => 'حجم الصورة لا يجب أن يتجاوز 2 ميجابايت.',
        ];
    }
}
