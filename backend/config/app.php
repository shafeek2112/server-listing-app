<?php

return [
    'app_key' => env('APP_KEY'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
];
