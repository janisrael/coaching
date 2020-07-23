<?php

$userFields = new learntotrade\salesforce\fields\UserFields;

return [

    'person' => [
        'id',
    ],
    
    'coaches' => [
        'id' => 'idNumber',
        'access_group' => ['SmartCharts', 'LTT'],
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'experience' => 'randomDigit',
        'experience_summary' => 'paragraph',
        'market_traded' => ['Forex', 'Stock Indices', 'Commodities'],
        'market_traded_summary' => 'paragraph',
        'style' => ['End of Day', 'Intra-day', 'Scalper'],
        'style_summary' => 'paragraph',
        'country' => 'country',
        'country_code' => 'countryCode',
        'languages' => ['English','Tagalog','Mandarin','Nemic','Jejemon'],
        'avatar' => '',
        'email' => '',
        'bio' => '',
        'business_role' => '',
    ],

    'sf_coaches' => [
        'id' => 'Id',
        'access_group' => $userFields::BUSINESS_DIVISION,
        'first_name' => $userFields::FIRST_NAME,
        'last_name' => $userFields::LAST_NAME,
        'experience' => $userFields::EXPERIENCE,
        'experience_summary' => $userFields::EXPERIENCE_SUMMARY,
        'market_traded' => $userFields::MARKET_TRADED,
        'market_traded_summary' => $userFields::MARKET_TRADED_SUMMARY,
        'style' => $userFields::TRADING_STYLE,
        'style_summary' => $userFields::TRADING_STYLE_SUMMARY,
        'country_code' => $userFields::REGION,
        'languages' => $userFields::LANGUAGES,
        'avatar' => $userFields::AVATAR,
        'email' => $userFields::EMAIL,
        'bio' => $userFields::BIO,
        'business_role' => $userFields::BUSINESS_ROLE,
    ],

    'schedule' => [
        'id',
    ]
];