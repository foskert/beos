<?php
return [
    'index' => [
        'message'=> 'Product List',
        'errors'=> 'Failed to retrieve product list.',
        'request'=>[
            'attributes'=>[
                'search'             => 'Search by name or description',
                'currency_id'        => 'Currency ID',
                'manufacturing_cost' => 'Manufacturing Cost',
                'min_price'          => 'Minimum Price',
                'max_price'          => 'Maximum Price',
                'high_cost'          => 'High Manufacturing Cost Filter',
                'paginate'           => 'Items per page',
                'orderBy'            => 'Order Direction',
                'sort_by'            => 'Sort By Column',
            ],
            'messages'=>[
                'string'       => 'The :attribute field must be a string.',
                'numeric'      => 'The :attribute field must be a number.',
                'integer'      => 'The :attribute field must be an integer.',
                'min' => [
                    'numeric'  => 'The :attribute field cannot be less than :min.',
                    'string'   => 'The :attribute field must have at least :min characters.',
                ],
                'max' => [
                    'numeric'  => 'The :attribute field cannot be greater than :max.',
                    'string'   => 'The :attribute field cannot have more than :max characters.',
                ],
                'exists'       => 'The selected :attribute the product does not exist in our records.',
                'in'           => 'The selected value for :attribute is invalid. It must be one of the following: :values.',
                'search.max'   => 'The search term is too long (maximum 100 characters).',
                'paginate.min' => 'You must request at least 5 items per page.',
                'paginate.max' => 'You cannot request more than 100 items per page.',
            ],
        ],
    ],
    'show' => [
        'message'=> 'Product Details',
        'errors'=> 'Failed to retrieve product details.',
        'request'=>[
            'attributes'=>[
                'id' => 'Product ID',
            ],
            'messages'=>[
                'required' => 'The :attribute field is required.',
                'integer'  => 'The :attribute field must be an integer.',
                'exists'   => 'The selected :attribute the product does not exist in our records.',
                'in'       => 'The selected value for :attribute is invalid. It must be one of the following: :values.',
            ],
        ],

    ],
    'destroy' => [
        'message'=> 'Product deleted successfully.',
        'request'=>[
            'attributes'=>[
                'id' => 'Product ID',
            ],
            'messages'=>[
                'required' => 'The :attribute field is required.',
                'integer'  => 'The :attribute field must be an integer.',
                'exists'   => 'The selected :attribute the product does not exist in our records.',
            ],
        ],
    ],
    'store' => [
        'message'=> 'Product created successfully.',
        'errors'=> 'Failed to create product.',
        'request'=>[
            'attributes'=>[
                'name'               => 'Product Name',
                'description'        => 'Product Description',
                'price'              => 'Product Price',
                'currency_id'        => 'Currency ID',
                'tax_cost'           => 'Tax Cost',
                'manufacturing_cost' => 'Manufacturing Cost',
            ],
            'messages'=>[
                'string'        =>  'The :attribute field must be a string.',
                'numeric'       =>  'The :attribute field must be a number.',
                'integer'       =>  'The :attribute field must be an integer.',
                'required'      =>  'The :attribute field is required.',
                'max'           =>  'The :attribute field cannot have more than :max characters.',
                'min'           =>  'The :attribute field cannot be less than :min.',
                'unique'        =>  'The :attribute has already been taken.',
                'exists'        =>  'The selected :attribute does not exist in our records.',
                'name.unique'   =>  'The product name has already been taken.',
            ],
        ],
    ],
    'update' => [
        'message'   => 'Product updated successfully.',
        'errors'    => 'Failed to update product.',
        'not_found' => 'Product not found.',
        'request'   =>[
            'attributes'=>[
                'name'               => 'Product Name',
                'description'        => 'Product Description',
                'price'              => 'Product Price',
                'currency_id'        => 'Currency ID',
                'tax_cost'           => 'Tax Cost',
                'manufacturing_cost' => 'Manufacturing Cost',
            ],
            'messages'=>[
                'string'        =>  'The :attribute field must be a string.',
                'numeric'       =>  'The :attribute field must be a number.',
                'integer'       =>  'The :attribute field must be an integer.',
                'required'      =>  'The :attribute field is required.',
                'max'           =>  'The :attribute field cannot have more than :max characters.',
                'min'           =>  'The :attribute field cannot be less than :min.',
                'unique'        =>  'The :attribute has already been taken.',
                'exists'        =>  'The selected :attribute does not exist in our records.',
                'in'            =>  'The product name has already been taken.'
            ],
        ],
    ],
    'audits' => [
        'message'=> 'Audit for Product ID: :id',
        'errors'=> 'Failed to retrieve product audit logs.',
    ],

];
