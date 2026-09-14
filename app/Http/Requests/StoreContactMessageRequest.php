<?php

namespace App\Http\Requests;

use App\Rules\MaxLinks;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Email;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Letters and the punctuation that turns up inside real names, and nothing
     * else: no digits, no slashes, no angle brackets. `\p{L}` keeps names
     * outside the Latin alphabet valid; the escaped code points are the
     * typographic apostrophes a phone substitutes for a plain one.
     */
    private const NAME_PATTERN = "/^[\p{L}\p{M}][\p{L}\p{M}\p{Zs}'\x{2018}\x{2019}\x{02BC}.\-]*$/u";

    /**
     * Control characters, tab / newline / carriage return excepted.
     */
    private const CONTROL_CHARACTERS = '/[\x00-\x08\x0B\x0C\x0E-\x1F]/';

    /**
     * Where to send the user back to when validation fails.
     *
     * The form lives in the contact section of the single page, so the
     * fragment puts the errors back in view.
     *
     * @var string
     */
    protected $redirect = '/#contact';

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
     * Every field bails on its first failure, so a visitor is told one clear
     * thing per field rather than a stack of consequences.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:'.self::NAME_PATTERN,
                'not_regex:/\b(?:https?|www)\b/i',
            ],
            'email' => [
                'bail',
                'required',
                'string',
                'max:254',
                $this->emailRule(),
            ],
            'message' => [
                'bail',
                'required',
                'string',
                'min:20',
                'max:5000',
                'not_regex:'.self::CONTROL_CHARACTERS,
                new MaxLinks(2),
            ],

            // Honeypot: people never see this field, bots fill it in.
            'website' => ['prohibited'],
        ];
    }

    /**
     * Strict RFC compliance, plus a check that the domain can actually receive
     * mail. The MX lookup is a live DNS query, so it is switchable, see
     * config/portfolio.php.
     */
    private function emailRule(): Email
    {
        return Rule::email()
            ->rfcCompliant(strict: true)
            ->when(
                (bool) config('portfolio.contact.verify_email_domain'),
                fn (Email $rule) => $rule->validateMxRecord(),
            );
    }

    /**
     * Prepare the data for validation.
     *
     * Runs of whitespace inside a name collapse to a single space, so the
     * name pattern sees what the visitor meant to type. Leading and trailing
     * whitespace is already gone by here, via the TrimStrings middleware.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && is_string($this->input('name'))) {
            $this->merge([
                'name' => preg_replace('/\s+/u', ' ', $this->string('name')->value()),
            ]);
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Please use letters only: no digits, links or symbols.',
            'name.not_regex' => 'Please use letters only: no digits, links or symbols.',
            'email.max' => 'That email address is longer than an email address can be.',
            'message.min' => 'Please write at least 20 characters so I know what you need.',
            'message.not_regex' => 'That message contains characters I cannot read.',
            'website.prohibited' => 'That submission looked automated. Please try again.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'website' => 'form',
        ];
    }
}
