<?php

namespace App\Http\Requests\AboutUs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutUsRequest extends FormRequest
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
            'main_title'     => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'main_subtitle'  => ['sometimes', 'required', 'string', 'min:10', 'max:500'],

            'section1_title' => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'section1_text'  => ['sometimes', 'required', 'string', 'min:20', 'max:1000'],

            'section2_title' => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'section2_text'  => ['sometimes', 'required', 'string', 'min:20', 'max:1000'],

            'section3_title' => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'section3_text'  => ['sometimes', 'required', 'string', 'min:20', 'max:1000'],

            'section4_title' => ['sometimes', 'required', 'string', 'min:5', 'max:255'],
            'section4_text'  => ['sometimes', 'required', 'string', 'min:20', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            // main
            'main_title.required'     => 'العنوان الرئيسي مطلوب.',
            'main_title.string'       => 'العنوان الرئيسي يجب أن يكون نصاً.',
            'main_title.min'          => 'العنوان الرئيسي يجب ألا يقل عن :min أحرف.',
            'main_title.max'          => 'العنوان الرئيسي يجب ألا يزيد عن :max أحرف.',

            'main_subtitle.required'  => 'العنوان الفرعي مطلوب.',
            'main_subtitle.string'    => 'العنوان الفرعي يجب أن يكون نصاً.',
            'main_subtitle.min'       => 'العنوان الفرعي يجب ألا يقل عن :min أحرف.',
            'main_subtitle.max'       => 'العنوان الفرعي يجب ألا يزيد عن :max أحرف.',

            // section 1
            'section1_title.required' => 'عنوان القسم الأول مطلوب.',
            'section1_title.string'   => 'عنوان القسم الأول يجب أن يكون نصاً.',
            'section1_title.min'      => 'عنوان القسم الأول يجب ألا يقل عن :min أحرف.',
            'section1_title.max'      => 'عنوان القسم الأول يجب ألا يزيد عن :max أحرف.',

            'section1_text.required'  => 'نص القسم الأول مطلوب.',
            'section1_text.string'    => 'نص القسم الأول يجب أن يكون نصاً.',
            'section1_text.min'       => 'نص القسم الأول يجب ألا يقل عن :min أحرف.',
            'section1_text.max'       => 'نص القسم الأول يجب ألا يزيد عن :max أحرف.',

            // section 2
            'section2_title.required' => 'عنوان القسم الثاني مطلوب.',
            'section2_title.string'   => 'عنوان القسم الثاني يجب أن يكون نصاً.',
            'section2_title.min'      => 'عنوان القسم الثاني يجب ألا يقل عن :min أحرف.',
            'section2_title.max'      => 'عنوان القسم الثاني يجب ألا يزيد عن :max أحرف.',

            'section2_text.required'  => 'نص القسم الثاني مطلوب.',
            'section2_text.string'    => 'نص القسم الثاني يجب أن يكون نصاً.',
            'section2_text.min'       => 'نص القسم الثاني يجب ألا يقل عن :min أحرف.',
            'section2_text.max'       => 'نص القسم الثاني يجب ألا يزيد عن :max أحرف.',

            // section 3
            'section3_title.required' => 'عنوان القسم الثالث مطلوب.',
            'section3_title.string'   => 'عنوان القسم الثالث يجب أن يكون نصاً.',
            'section3_title.min'      => 'عنوان القسم الثالث يجب ألا يقل عن :min أحرف.',
            'section3_title.max'      => 'عنوان القسم الثالث يجب ألا يزيد عن :max أحرف.',

            'section3_text.required'  => 'نص القسم الثالث مطلوب.',
            'section3_text.string'    => 'نص القسم الثالث يجب أن يكون نصاً.',
            'section3_text.min'       => 'نص القسم الثالث يجب ألا يقل عن :min أحرف.',
            'section3_text.max'       => 'نص القسم الثالث يجب ألا يزيد عن :max أحرف.',

            // section 4
            'section4_title.required' => 'عنوان القسم الرابع مطلوب.',
            'section4_title.string'   => 'عنوان القسم الرابع يجب أن يكون نصاً.',
            'section4_title.min'      => 'عنوان القسم الرابع يجب ألا يقل عن :min أحرف.',
            'section4_title.max'      => 'عنوان القسم الرابع يجب ألا يزيد عن :max أحرف.',

            'section4_text.required'  => 'نص القسم الرابع مطلوب.',
            'section4_text.string'    => 'نص القسم الرابع يجب أن يكون نصاً.',
            'section4_text.min'       => 'نص القسم الرابع يجب ألا يقل عن :min أحرف.',
            'section4_text.max'       => 'نص القسم الرابع يجب ألا يزيد عن :max أحرف.',
        ];
    }

    public function attributes(): array
    {
        return [
            'main_title'     => 'العنوان الرئيسي',
            'main_subtitle'  => 'العنوان الفرعي',
            'section1_title' => 'عنوان القسم الأول',
            'section1_text'  => 'نص القسم الأول',
            'section2_title' => 'عنوان القسم الثاني',
            'section2_text'  => 'نص القسم الثاني',
            'section3_title' => 'عنوان القسم الثالث',
            'section3_text'  => 'نص القسم الثالث',
            'section4_title' => 'عنوان القسم الرابع',
            'section4_text'  => 'نص القسم الرابع',
        ];
    }
}
