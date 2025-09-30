<?php

namespace App\Http\Requests\SubscribeDetails;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscribeDetailsRequest extends FormRequest
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
            'title'       => 'sometimes|required|string|min:3|max:255',
            'description' => 'sometimes|required|string|min:10',
            'price'       => 'sometimes|required|integer|min:0',
            'image'       => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'features'    => 'sometimes|required|string|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'حقل العنوان مطلوب عند الإرسال.',
            'title.string'         => 'العنوان يجب أن يكون نصًا.',
            'title.min'            => 'العنوان يجب أن يحتوي على 3 أحرف على الأقل.',
            'title.max'            => 'العنوان يجب ألا يتجاوز 255 حرفًا.',

            'description.required' => 'الوصف مطلوب عند الإرسال.',
            'description.string'   => 'الوصف يجب أن يكون نصًا.',
            'description.min'      => 'الوصف يجب أن يحتوي على 10 أحرف على الأقل.',

            'price.required'       => 'السعر مطلوب عند الإرسال.',
            'price.integer'        => 'السعر يجب أن يكون رقمًا صحيحًا.',
            'price.min'            => 'السعر يجب أن يكون أكبر من أو يساوي 0.',

            'image.required'       => 'الصورة مطلوبة عند الإرسال.',
            'image.image'          => 'يجب أن تكون القيمة ملف صورة صالحًا.',
            'image.mimes'          => 'امتدادات الصورة المسموح بها: jpeg, png, jpg, gif, svg, webp.',
            'image.max'            => 'حجم الصورة يجب ألا يتجاوز 2 ميغابايت.',

            'features.required'    => 'المميزات مطلوبة عند الإرسال.',
            'features.string'      => 'المميزات يجب أن تكون نصًا.',
            'features.min'         => 'المميزات يجب أن تحتوي على 5 أحراف على الأقل.',
        ];
    }
}
