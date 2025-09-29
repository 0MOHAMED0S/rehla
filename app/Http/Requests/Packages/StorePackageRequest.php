<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'name'            => 'required|string|min:3|max:255|unique:packages,name',
            'sessions'        => 'required|string|min:2|max:255',
            'price'           => 'required|numeric|min:1|max:100000',
            'features'        => 'required|string|min:3|max:5000',
            'is_most_popular' => 'boolean',
            'status'          => 'sometimes|in:0,1',
        ];
    }

    /**
     * Custom error messages in Arabic.
     */
    public function messages(): array
    {
        return [
            // الاسم
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string'   => 'الاسم يجب أن يكون نصاً.',
            'name.min'      => 'الاسم يجب أن يحتوي على 3 أحرف على الأقل.',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرف.',
            'name.unique'   => 'هذا الاسم مستخدم بالفعل، يرجى اختيار اسم آخر.',

            // الجلسات
            'sessions.required' => 'حقل الجلسات مطلوب.',
            'sessions.string'   => 'الجلسات يجب أن تكون نصاً.',
            'sessions.min'      => 'الجلسات يجب أن تحتوي على حرفين على الأقل.',
            'sessions.max'      => 'الجلسات يجب ألا تتجاوز 255 حرف.',

            // السعر
            'price.required' => 'حقل السعر مطلوب.',
            'price.numeric'  => 'السعر يجب أن يكون رقم.',
            'price.min'      => 'السعر يجب أن يكون على الأقل 1 جنيه.',
            'price.max'      => 'السعر يجب ألا يتجاوز 100000 جنيه.',

            // المميزات
            'features.required' => 'حقل المميزات مطلوب.',
            'features.string'   => 'المميزات يجب أن تكون نصاً.',
            'features.min'      => 'المميزات يجب أن تحتوي على 3 أحرف على الأقل.',
            'features.max'      => 'المميزات يجب ألا تتجاوز 5000 حرف.',

            // الأكثر شيوعاً
            'is_most_popular.boolean' => 'القيمة لحقل الأكثر شيوعاً يجب أن تكون true أو false.',

            // الحالة
            'status.in' => 'الحالة يجب أن تكون 0 (غير مفعلة) أو 1 (مفعلة).',
        ];
    }
}
