<?php

return [
    'auto_defaulter_first_reminder' => [
        'daily_limit' => env('AUTO_DEFAULTER_DAILY_LIMIT', 100),
        'quiet_hours_start' => env('AUTO_DEFAULTER_QUIET_HOURS_START', '21:00'),
        'quiet_hours_end' => env('AUTO_DEFAULTER_QUIET_HOURS_END', '06:00'),
    ],
];
