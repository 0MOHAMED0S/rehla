<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['sometimes', 'string', 'min:3', 'max:255'],
            'description'           => ['sometimes', 'string', 'min:10', 'max:1000'],
            'features_text'         => ['sometimes', 'string', 'min:5', 'max:2000'],
            'electronic_copy_price' => ['nullable', 'integer', 'min:0'],
            'printed_copy_price'    => ['nullable', 'integer', 'min:0'],
            'offered_price'         => ['nullable', 'integer', 'min:0'],
            'fixed_price'           => ['nullable', 'integer', 'min:0'],
            'image'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $electronic = $this->input('electronic_copy_price');
            $printed    = $this->input('printed_copy_price');
            $offer      = $this->input('offered_price');
            $fixed      = $this->input('fixed_price');

            // إذا تم اختيار fixed_price -> لا يمكن إدخال أي أسعار أخرى
            if (!is_null($fixed)) {
                if (!is_null($electronic) || !is_null($printed) || !is_null($offer)) {
                    $validator->errors()->add(
                        'fixed_price',
                        'عند اختيار السعر الثابت لا يمكن إدخال أي نوع آخر من الأسعار.'
                    );
                }
            } else {
                // إذا لم يكن fixed_price موجود -> لازم واحد على الأقل من الباقي
                if (is_null($electronic) && is_null($printed) && is_null($offer)) {
                    $validator->errors()->add(
                        'price',
                        'يجب إدخال سعر النسخة الإلكترونية أو المطبوعة أو السعر المعروض على الأقل.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            // الاسم
            'name.string' => 'اسم المنتج يجب أن يكون نصاً.',
            'name.min'    => 'اسم المنتج يجب أن يحتوي على 3 أحرف على الأقل.',
            'name.max'    => 'اسم المنتج لا يجب أن يتجاوز 255 حرفاً.',

            // الوصف
            'description.string' => 'الوصف يجب أن يكون نصاً.',
            'description.min'    => 'الوصف يجب أن يحتوي على 10 أحرف على الأقل.',
            'description.max'    => 'الوصف لا يجب أن يتجاوز 1000 حرف.',

            // المميزات
            'features_text.string' => 'المميزات يجب أن تكون نصاً.',
            'features_text.min'    => 'المميزات يجب أن تحتوي على 5 أحرف على الأقل.',
            'features_text.max'    => 'المميزات لا يجب أن تتجاوز 2000 حرف.',

            // الأسعار
            'electronic_copy_price.integer' => 'سعر النسخة الإلكترونية يجب أن يكون رقماً صحيحاً.',
            'electronic_copy_price.min'     => 'سعر النسخة الإلكترونية لا يمكن أن يكون أقل من 0.',

            'printed_copy_price.integer' => 'سعر النسخة المطبوعة يجب أن يكون رقماً صحيحاً.',
            'printed_copy_price.min'     => 'سعر النسخة المطبوعة لا يمكن أن يكون أقل من 0.',

            'offered_price.integer' => 'السعر المعروض يجب أن يكون رقماً صحيحاً.',
            'offered_price.min'     => 'السعر المعروض لا يمكن أن يكون أقل من 0.',

            'fixed_price.integer' => 'السعر الثابت يجب أن يكون رقماً صحيحاً.',
            'fixed_price.min'     => 'السعر الثابت لا يمكن أن يكون أقل من 0.',

            // الصورة
            'image.image' => 'يجب أن يكون الملف صورة.',
            'image.mimes' => 'يجب أن تكون الصورة بصيغة: jpeg, png, jpg, gif, webp.',
            'image.max'   => 'حجم الصورة لا يجب أن يتجاوز 2 ميجابايت.',
        ];
    }
}
