<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Availability badge
    |--------------------------------------------------------------------------
    |
    | Short line shown above the hero headline, e.g. "Open to new work".
    | Leave it unset and the badge is not rendered at all.
    |
    */

    'availability' => env('PORTFOLIO_AVAILABILITY'),

    /*
    |--------------------------------------------------------------------------
    | Stat row
    |--------------------------------------------------------------------------
    |
    | The ruled row of numbers sitting between the hero and the about section.
    |
    */

    'show_stats' => (bool) env('PORTFOLIO_SHOW_STATS', true),

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    |
    | verify_email_domain makes the contact form check that the submitted
    | address has a DNS MX record, so a typo'd or invented domain is caught at
    | the form rather than at reply time. It costs one live DNS lookup per
    | submission; turn it off if the host cannot resolve DNS.
    |
    */

    'contact' => [
        'verify_email_domain' => (bool) env('PORTFOLIO_VERIFY_EMAIL_DOMAIN', true),
    ],

];
