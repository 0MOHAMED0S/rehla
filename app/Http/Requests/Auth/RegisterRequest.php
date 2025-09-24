<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'الاسم مطلوب.',
            'name.string'       => 'الاسم يجب أن يكون نص.',
            'name.min'          => 'الاسم يجب ألا يقل عن 3 أحرف.',
            'name.max'          => 'الاسم يجب ألا يزيد عن 100 حرف.',

            'email.required'    => 'البريد الإلكتروني مطلوب.',
            'email.email'       => 'البريد الإلكتروني غير صالح.',
            'email.max'         => 'البريد الإلكتروني يجب ألا يزيد عن 255 حرف.',
            'email.unique'      => 'البريد الإلكتروني مستخدم بالفعل.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string'   => 'كلمة المرور يجب أن تكون نص.',
            'password.min'      => 'كلمة المرور يجب ألا تقل عن 6 أحرف.',
            'password.max'      => 'كلمة المرور يجب ألا تزيد عن 100 حرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
        ];
    }
}
