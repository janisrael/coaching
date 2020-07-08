<?php

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
        'languages' => ['English','Tagalog','Mandarin','Nemic','Jejemon']
    ],

    'schedule' => [
        'id',
    ]
];