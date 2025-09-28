<?php

namespace App\Http\Requests\Child;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest
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
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',

            'age'        => 'required|integer|min:1|max:18',
            'gender'     => 'required|string|in:ذكر,أنثى',
            'interests'  => 'required|string',
            'strengths'  => 'required|string',
            'avatar'     => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ];
    }
    public function messages(): array
    {
        return [
            'name.required'      => 'اسم الطفل مطلوب',
            'email.required'     => 'البريد الإلكتروني مطلوب',
            'email.unique'       => 'هذا البريد مستخدم بالفعل',
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',

            'age.required'       => 'العمر مطلوب',
            'age.integer'        => 'العمر يجب أن يكون رقمًا',
            'gender.required'    => 'الجنس مطلوب',
            'gender.in'          => 'الجنس يجب أن يكون ذكر أو أنثى',

            'interests.required' => 'اهتمامات الطفل مطلوبة',
            'strengths.required' => 'نقاط قوة الطفل مطلوبة',
            'avatar.required'    => 'صورة الطفل مطلوبة',

        ];
    }
}
