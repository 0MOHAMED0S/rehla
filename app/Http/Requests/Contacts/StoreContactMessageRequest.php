<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
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
            'name'               => 'required|string|min:3|max:255',
            'email'              => 'required|email|min:5|max:255',
            'contact_subject_id' => 'required|exists:contact_subjects,id',
            'message'            => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'الاسم مطلوب',
            'name.min'                    => 'الاسم يجب ألا يقل عن 3 أحرف',
            'name.max'                    => 'الاسم يجب ألا يزيد عن 255 حرف',

            'email.required'              => 'البريد الإلكتروني مطلوب',
            'email.email'                 => 'البريد الإلكتروني غير صالح',
            'email.min'                   => 'البريد الإلكتروني يجب ألا يقل عن 5 أحرف',
            'email.max'                   => 'البريد الإلكتروني يجب ألا يزيد عن 255 حرف',

            'contact_subject_id.required' => 'الموضوع مطلوب',
            'contact_subject_id.exists'   => 'الموضوع غير موجود',

            'message.required'            => 'الرسالة مطلوبة',
            'message.min'                 => 'الرسالة يجب ألا تقل عن 10 أحرف',
            'message.max'                 => 'الرسالة يجب ألا تزيد عن 1000 حرف',
        ];
    }
}
