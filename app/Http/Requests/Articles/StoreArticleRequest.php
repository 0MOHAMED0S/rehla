<?php

namespace App\Http\Requests\Articles;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title'       => 'required|string|min:5|max:255',
            'slug'        => 'required|string|unique:articles,slug|max:255',
            'text'        => 'required|string|min:50|max:10000',
            'image'       => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'author_name' => 'required|string|max:255',
            'status'      => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'عنوان المقال مطلوب.',
            'title.string'   => 'عنوان المقال يجب أن يكون نص.',
            'title.min'      => 'عنوان المقال يجب ألا يقل عن 5 أحرف.',
            'title.max'      => 'عنوان المقال يجب ألا يزيد عن 255 حرف.',

            'slug.required' => 'الرابط (Slug) مطلوب.',
            'slug.string'   => 'الرابط (Slug) يجب أن يكون نص.',
            'slug.unique'   => 'هذا الرابط مستخدم من قبل، اختر رابط آخر.',
            'slug.max'      => 'الرابط (Slug) يجب ألا يزيد عن 255 حرف.',

            'text.required' => 'المحتوى مطلوب.',
            'text.string'   => 'المحتوى يجب أن يكون نص.',
            'text.min'      => 'المحتوى يجب ألا يقل عن 50 حرف.',
            'text.max'      => 'المحتوى يجب ألا يزيد عن 10000 حرف.',

            'image.required' => 'الصورة مطلوبة.',
            'image.image'    => 'الملف يجب أن يكون صورة.',
            'image.mimes'    => 'الصورة يجب أن تكون من نوع: jpg, jpeg, png, gif.',
            'image.max'      => 'حجم الصورة يجب ألا يتجاوز 2 ميجا بايت.',

            'author_name.required' => 'اسم الكاتب مطلوب.',
            'author_name.string'   => 'اسم الكاتب يجب أن يكون نص.',
            'author_name.max'      => 'اسم الكاتب يجب ألا يزيد عن 255 حرف.',

            'status.required' => 'حالة المقال مطلوبة.',
            'status.boolean'  => 'قيمة حالة المقال يجب أن تكون إما 0 (مسودة) أو 1 (منشور).',
        ];
    }
}
