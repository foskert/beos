<?php
return [
    'request'=> [
        'attributes'=>[
            'email'    => 'email address',
            'password' => 'password',
        ],
        'messages'=>[
            'string'   => 'The :attribute field must be a string.',
            'required' => 'The :attribute field is required.',
            'max'      => 'The :attribute field cannot have more than :max characters.',
            'email'    => 'The :attribute field must be a valid email address.',
        ],
        'authorization' =>[
            'value'   => 'Not authorization.',
            'message' => 'You are not authorized to perform this action.',
            'error'   => 'the number of login attempts, successful',
        ],
        'errors'=>[
            'message'=> 'failed validation.',
        ]
    ],
    'login'=> [
        'susses'=> 'Susses Login',
        'message'=> 'These credentials do not match our records.',
    ]
];
