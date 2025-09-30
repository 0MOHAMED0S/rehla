<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'name'             => 'required|string|min:3|max:255',
            'children_id'      => 'nullable',
            'image1'           => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'image2'           => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'image3'           => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'child_attributes' => 'required|string|min:3|max:5000',
            'educational_goal' => 'required|string|min:5|max:5000',
            'price'            => ['required', 'in:electronic_copy_price,fixed_price,printed_copy_price,offered_price'],
            'shipping_id'      => 'required|exists:shippings,id',
            'address'          => 'required|string|min:5|max:5000',
            'phone'            => 'required|string|regex:/^[0-9]{10,15}$/',
            'age'              => 'required|integer|min:1|max:18',
            'gender'           => 'required|in:male,female',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.string'   => 'الاسم يجب أن يكون نصاً.',
            'name.min'      => 'الاسم يجب أن يحتوي على 3 أحرف على الأقل.',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرف.',

            'children_id.exists' => 'الطفل المحدد غير موجود.',

            'image1.required' => 'الصورة الأولى مطلوبة.',
            'image1.image'    => 'الصورة الأولى يجب أن تكون صورة صحيحة.',
            'image1.mimes'    => 'الصورة الأولى يجب أن تكون بصيغة jpg أو jpeg أو png.',
            'image1.max'      => 'الصورة الأولى يجب ألا تتجاوز 2MB.',

            'image2.required' => 'الصورة الثانية مطلوبة.',
            'image2.image'    => 'الصورة الثانية يجب أن تكون صورة صحيحة.',
            'image2.mimes'    => 'الصورة الثانية يجب أن تكون بصيغة jpg أو jpeg أو png.',
            'image2.max'      => 'الصورة الثانية يجب ألا تتجاوز 2MB.',

            'image3.required' => 'الصورة الثالثة مطلوبة.',
            'image3.image'    => 'الصورة الثالثة يجب أن تكون صورة صحيحة.',
            'image3.mimes'    => 'الصورة الثالثة يجب أن تكون بصيغة jpg أو jpeg أو png.',
            'image3.max'      => 'الصورة الثالثة يجب ألا تتجاوز 2MB.',

            'child_attributes.required' => 'صفات الطفل مطلوبة.',
            'child_attributes.string'   => 'صفات الطفل يجب أن تكون نصاً.',
            'child_attributes.min'      => 'صفات الطفل يجب أن تحتوي على 3 أحرف على الأقل.',
            'child_attributes.max'      => 'صفات الطفل يجب ألا تتجاوز 5000 حرف.',

            'educational_goal.required' => 'الهدف التربوي مطلوب.',
            'educational_goal.string'   => 'الهدف التربوي يجب أن يكون نصاً.',
            'educational_goal.min'      => 'الهدف التربوي يجب أن يحتوي على 5 أحرف على الأقل.',
            'educational_goal.max'      => 'الهدف التربوي يجب ألا يتجاوز 5000 حرف.',

            'price.required' => 'حقل السعر مطلوب.',
            'price.in'       => 'السعر يجب أن يكون واحدًا من القيم التالية: electronic_copy_price, fixed_price, printed_copy_price, offered_price.',


            'shipping_id.required' => 'خدمة الشحن مطلوبة.',
            'shipping_id.exists'   => 'خدمة الشحن المحددة غير موجودة.',


            'address.required' => 'العنوان مطلوب.',
            'address.string'   => 'العنوان يجب أن يكون نصاً.',
            'address.min'      => 'العنوان يجب أن يحتوي على 5 أحرف على الأقل.',
            'address.max'      => 'العنوان يجب ألا تتجاوز 5000 حرف.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string'   => 'رقم الهاتف يجب أن يكون نصاً.',
            'phone.regex'    => 'رقم الهاتف يجب أن يكون بين 10 و 15 رقماً.',

            'age.required' => 'العمر مطلوب.',
            'age.integer'  => 'العمر يجب أن يكون رقماً صحيحاً.',
            'age.min'      => 'العمر يجب أن يكون أكبر من 0.',
            'age.max'      => 'العمر يجب ألا يتجاوز 18 سنة.',

            'gender.required' => 'النوع مطلوب.',
            'gender.in'       => 'النوع يجب أن يكون ذكر (male) أو أنثى (female).',
        ];
    }
}
