<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A "Get in touch" enquiry from the homepage.
 *
 * Public by design -- anyone may ask a question -- so there is no
 * authorization gate; abuse is handled by the route's rate limit and the
 * honeypot field checked in EnquiryController.
 */
class StoreEnquiryRequest extends FormRequest
{
    /**
     * Everything a visitor can say they are interested in: the five lending
     * products in config/marketing.php, plus the advisory service and a
     * catch-all. The form renders this list and validation accepts only it,
     * so the two can never drift apart.
     *
     * @return list<string>
     */
    public static function interests(): array
    {
        return collect(config('marketing.products'))
            ->pluck('name')
            ->push('Financial advisory', 'Something else')
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function branches(): array
    {
        return array_column(config('company.branches'), 'name');
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s]{7,}$/'],
            'email' => ['nullable', 'email', 'max:190'],
            'interest' => ['required', Rule::in(self::interests())],
            'branch' => ['required', Rule::in(self::branches())],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tell us your name so we know who to ask for.',
            'phone.required' => 'We need a number to call or WhatsApp you back on.',
            'phone.regex' => 'That does not look like a phone number.',
            'interest.required' => 'Choose what you would like to talk about.',
            'branch.required' => 'Choose the branch nearest to you.',
        ];
    }
}
