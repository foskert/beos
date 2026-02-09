<?php
return [
    'index' => [
        'message'=> 'Price List',
        'errors'=> 'Failed to retrieve price list.',
    ],
    'store' => [
        'message' => 'Price created successfully.',
        'errors'  => 'Failed to create price.',
        'request' => [
            'attributes' => [
                'product_id'         => 'Product ID',
                'currency_id'        => 'Currency ID',
                'price'              => 'Price',
            ],
            'messages'   => [
                'required'           => 'The :attribute field is required.',
                'numeric'            => 'The :attribute must be a number.',
                'min'                => 'The :attribute must be at least :min.',
                'exists' =>[
                    'product_id'     => 'The selected product ID is invalid.',
                    'currency_id'    => 'The selected currency ID is invalid.',
                ]
            ],
        ],
    ],
    'audits' => [
        'failed'=> 'Audit for Price ID: :id',
        'errors'=> 'Failed to retrieve price audit logs.',
    ],

];
