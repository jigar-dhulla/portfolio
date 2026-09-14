<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Cap how many links a piece of text may contain.
 *
 * A genuine enquiry occasionally carries a link: someone's company, a repo,
 * the thing they want help with. A pile of them is link spam.
 */
class MaxLinks implements ValidationRule
{
    /**
     * Matches http(s):// URLs and bare hosts written as "www.something".
     */
    private const LINK_PATTERN = '/\b(?:https?:\/\/|www\.)\S+/i';

    public function __construct(private int $limit = 2) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (preg_match_all(self::LINK_PATTERN, $value) > $this->limit) {
            $fail('The :attribute may not contain more than :count links.')->translate([
                'count' => $this->limit,
            ]);
        }
    }
}
