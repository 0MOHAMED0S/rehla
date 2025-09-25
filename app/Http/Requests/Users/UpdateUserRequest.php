<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        'name'  => 'sometimes|string|max:255',
        'email' => [
            'sometimes',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($this->route('user')),
        ],
    ];
}



    public function messages(): array
    {
        return [
            'name.string'   => 'الاسم يجب أن يكون نص.',
            'name.max'      => 'الاسم يجب ألا يزيد عن 255 حرف.',
            'email.string'  => 'البريد الإلكتروني يجب أن يكون نص.',
            'email.email'   => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.max'     => 'البريد الإلكتروني يجب ألا يزيد عن 255 حرف.',
            'email.unique'  => 'البريد الإلكتروني مستخدم بالفعل.',
        ];
    }
}
