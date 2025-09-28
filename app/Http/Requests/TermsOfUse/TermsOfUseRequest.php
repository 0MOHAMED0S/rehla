<?php

namespace App\Http\Requests\TermsOfUse;

use Illuminate\Foundation\Http\FormRequest;

class TermsOfUseRequest extends FormRequest
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
            'main_title'     => ['sometimes','required','string','min:5','max:255'],
            'main_subtitle'  => ['sometimes','required','string','min:10','max:500'],

            'section1_title' => ['sometimes','required','string','min:5','max:255'],
            'section1_text'  => ['sometimes','required','string','min:20','max:1000'],

            'section2_title' => ['sometimes','required','string','min:5','max:255'],
            'section2_text'  => ['sometimes','required','string','min:20','max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'main_title.required'     => 'العنوان الرئيسي مطلوب.',
            'main_title.min'          => 'العنوان الرئيسي يجب ألا يقل عن :min أحرف.',
            'main_title.max'          => 'العنوان الرئيسي يجب ألا يزيد عن :max أحرف.',

            'main_subtitle.required'  => 'العنوان الفرعي مطلوب.',
            'main_subtitle.min'       => 'العنوان الفرعي يجب ألا يقل عن :min أحرف.',
            'main_subtitle.max'       => 'العنوان الفرعي يجب ألا يزيد عن :max أحرف.',

            'section1_title.required' => 'عنوان الموافقة مطلوب.',
            'section1_title.min'      => 'عنوان الموافقة يجب ألا يقل عن :min أحرف.',
            'section1_title.max'      => 'عنوان الموافقة يجب ألا يزيد عن :max أحرف.',

            'section1_text.required'  => 'نص الموافقة مطلوب.',
            'section1_text.min'       => 'نص الموافقة يجب ألا يقل عن :min أحرف.',
            'section1_text.max'       => 'نص الموافقة يجب ألا يزيد عن :max أحرف.',

            'section2_title.required' => 'عنوان الملكية الفكرية مطلوب.',
            'section2_title.min'      => 'عنوان الملكية الفكرية يجب ألا يقل عن :min أحرف.',
            'section2_title.max'      => 'عنوان الملكية الفكرية يجب ألا يزيد عن :max أحرف.',

            'section2_text.required'  => 'نص الملكية الفكرية مطلوب.',
            'section2_text.min'       => 'نص الملكية الفكرية يجب ألا يقل عن :min أحرف.',
            'section2_text.max'       => 'نص الملكية الفكرية يجب ألا يزيد عن :max أحرف.',
        ];
    }

    public function attributes(): array
    {
        return [
            'main_title'     => 'العنوان الرئيسي',
            'main_subtitle'  => 'العنوان الفرعي',
            'section1_title' => 'عنوان الموافقة',
            'section1_text'  => 'نص الموافقة',
            'section2_title' => 'عنوان الملكية الفكرية',
            'section2_text'  => 'نص الملكية الفكرية',
        ];
    }
}
