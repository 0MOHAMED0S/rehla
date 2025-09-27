<?php

namespace App\Http\Requests\LogoAndLinks;

use Illuminate\Foundation\Http\FormRequest;

class LogoAndLinkRequest extends FormRequest
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
        'main_logo'                => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        'creative_writing_logo'    => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        'gate_inha_lak_image'      => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        'gate_start_journey_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        'about_page_image'         => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        'facebook_link'            => ['sometimes', 'url'],
        'twitter_link'             => ['sometimes', 'url'],
        'instagram_link'           => ['sometimes', 'url'],
    ];
}

public function messages(): array
{
    return [
        // الصور
        'main_logo.image'                => 'شعار الموقع الرئيسي يجب أن يكون ملف صورة.',
        'main_logo.mimes'                => 'شعار الموقع الرئيسي يجب أن يكون بصيغة: jpeg, png, jpg, gif, webp.',
        'main_logo.max'                  => 'حجم شعار الموقع الرئيسي لا يجب أن يتجاوز 2 ميجابايت.',

        'creative_writing_logo.image'    => 'شعار برنامج الكتابة الإبداعية يجب أن يكون ملف صورة.',
        'creative_writing_logo.mimes'    => 'شعار برنامج الكتابة الإبداعية يجب أن يكون بصيغة: jpeg, png, jpg, gif, webp.',
        'creative_writing_logo.max'      => 'حجم شعار برنامج الكتابة الإبداعية لا يجب أن يتجاوز 2 ميجابايت.',

        'gate_inha_lak_image.image'      => 'صورة بوابة "إنها لك" يجب أن تكون ملف صورة.',
        'gate_inha_lak_image.mimes'      => 'صورة بوابة "إنها لك" يجب أن تكون بصيغة: jpeg, png, jpg, gif, webp.',
        'gate_inha_lak_image.max'        => 'حجم صورة بوابة "إنها لك" لا يجب أن يتجاوز 2 ميجابايت.',

        'gate_start_journey_image.image' => 'صورة بوابة "بداية الرحلة" يجب أن تكون ملف صورة.',
        'gate_start_journey_image.mimes' => 'صورة بوابة "بداية الرحلة" يجب أن تكون بصيغة: jpeg, png, jpg, gif, webp.',
        'gate_start_journey_image.max'   => 'حجم صورة بوابة "بداية الرحلة" لا يجب أن يتجاوز 2 ميجابايت.',

        'about_page_image.image'         => 'صورة صفحة "عنا" يجب أن تكون ملف صورة.',
        'about_page_image.mimes'         => 'صورة صفحة "عنا" يجب أن تكون بصيغة: jpeg, png, jpg, gif, webp.',
        'about_page_image.max'           => 'حجم صورة صفحة "عنا" لا يجب أن يتجاوز 2 ميجابايت.',

        // الروابط
        'facebook_link.url'              => 'رابط صفحة فيسبوك يجب أن يكون رابط صالح.',
        'twitter_link.url'               => 'رابط صفحة تويتر يجب أن يكون رابط صالح.',
        'instagram_link.url'             => 'رابط صفحة انستغرام يجب أن يكون رابط صالح.',
    ];
}


}
