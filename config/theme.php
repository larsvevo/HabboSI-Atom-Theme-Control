<?php

return [
    'api' => [
        'badge_endpoint_enabled' => env('BADGE_ENDPOINT_ENABLED', false),
        'furniture_endpoint_enabled' => env('FURNITURE_ENDPOINT_ENABLED', false),
        'home_category_endpoint_enabled' => env('HOME_CATEGORY_ENDPOINT_ENABLED', false),
        'home_item_endpoint_enabled' => env('HOME_ITEM_ENDPOINT_ENABLED', false),
        'online_count_endpoint_enabled' => env('ONLINE_COUNT_ENDPOINT_ENABLED', false),
        'online_user_endpoint_enabled' => env('ONLINE_USER_ENDPOINT_ENABLED', false),
        'trade_log_endpoint_enabled' => env('TRADE_LOG_ENDPOINT_ENABLED', false),
        'user_endpoint_enabled' => env('USER_ENDPOINT_ENABLED', false),
        'website_article_endpoint_enabled' => env('WEBSITE_ARTICLE_ENDPOINT_ENABLED', false),
    ],
    'reactions' => [
        'bad', 'crying', 'good', 'happy', 'taut', 'impatient', 'inlove', 'laugh', 'proud', 'wow',
        'shameful', 'shameless', 'sleeping', 'smile', 'tongue', 'wink', 'disgusted', 'angry', 'lgbt', 'heart2', 'bobba', 'poop',
        'like', 'unlike', 'fire', 'eyes', 'crown', 'star', 'heart',
    ],
];
