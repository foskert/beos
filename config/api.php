<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Defaults
    |--------------------------------------------------------------------------
    |
    |
    |
    */
    'defaults' => [
        'authorization'      => false,
        'paginate'           => 10,
        'order_by'           => 'DESC',
        'sort_by'            => 'id',
        'format_date'        => 'Y-m-d',
        'format_date_time'   => 'Y-m-d H:i:s',
        'decimal'            => 2,
        'decimal_separator'  => '.',
        'thousand_separator' => ',',
        'tries'              => 5,
        'backoff'            => [60, 300, 600],
        'maxAttempts'        => 5,
    ],
];
