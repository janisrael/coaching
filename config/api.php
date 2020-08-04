<?php

$userFields = new learntotrade\salesforce\fields\UserFields;
$coachingSessionFields = new learntotrade\salesforce\fields\CoachingSessionFields;
$saleFields = new learntotrade\salesforce\fields\SaleFields;

return [

    'person' => [
        'id',
    ],

    'sf_person' => [
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
        'coach_image' => 'imageUrl',
        'email' => 'email',
        'bio' => 'paragraph',
        'business_role' => 'word',
        'speaker_image' => 'imageUrl',
        'region' => '',
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
        'country_code' => $userFields::COACH_COUNTRY,
        'languages' => $userFields::LANGUAGES,
        'coach_image' => $userFields::COACH_IMAGE,
        'email' => $userFields::EMAIL,
        'bio' => $userFields::BIO,
        'business_role' => $userFields::BUSINESS_ROLE,
        'speaker_image' => $userFields::AVATAR,
        'region' => $userFields::REGION,
    ],

    'schedule' => [
        'id' => 'idNumber',
        'date' => '',
        'start_time' => '',
        'end_time' => '',
        'status' => ['Pending', 'Booked', 'Attended', 'Cancelled'],
        'availability_type' => ['Can do either', 'Remote Only', 'In-house only', 'Group'],
        'coach_id' => '',
        'location' => ['Inhouse', 'Remote'],
        'sale_id' => '',
        'customer_name' => ''
    ],

    'sf_schedule' => [
        'id' => 'Id',
        'date' => $coachingSessionFields::DATE,
        'start_time' => $coachingSessionFields::START_TIME,
        'end_time' => $coachingSessionFields::END_TIME,
        'status' => $coachingSessionFields::STATUS,
        'availability_type' => $coachingSessionFields::AVAILABILITY_TYPE,
        'coach_id' => $coachingSessionFields::COACH,
        'location' => $coachingSessionFields::LOCATION,
        'sale_id' => $coachingSessionFields::SALE,
        'customer_name' => $coachingSessionFields::CUSTOMER_NAME,
        'zoom_url' => $coachingSessionFields::ZOOM_URL,
    ],

    'sale' => [
        'id' => 'idNumber',
        'customer' => '',
        'date' => '', 
        'record_type_id' => '',
        'product' => '',
        'sessions' => '',
        'sessions_remaining' => '',
        'sessions_booked' => '',
        'sessions_cancelled' => '',
        'sessions_recredited' => '',
        'sessions_expiry' => '',
    ],

    'sf_sale' => [
        'id' => 'Id',
        'customer' => $saleFields::CUSTOMER,
        'date' => $saleFields::DATE, 
        'record_type_id' => $saleFields::RECORD_TYPE_ID,
        'product' => $saleFields::PRODUCT,
        'sessions' => $saleFields::SESSIONS,
        'sessions_remaining' => $saleFields::SESSIONS_REMAINING,
        'sessions_booked' => $saleFields::SESSIONS_BOOKED,
        'sessions_cancelled' => $saleFields::SESSIONS_CANCELLED,
        'sessions_recredited' => $saleFields::SESSIONS_RECREDITED,
        'sessions_expiry' => $saleFields::SESSIONS_EXPIRY,
    ]
];