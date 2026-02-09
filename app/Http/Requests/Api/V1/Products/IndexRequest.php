<?php

namespace App\Http\Requests\Api\V1\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(config('api.defaults.authorization', false)){
            return true;
        }
        if (!Auth::check()) {
            return false;
        }
        return  $this->user()->can('index', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search'             => 'nullable|string|max:100',
            'currency_id'        => 'nullable|integer|exists:currencies,id',
            'manufacturing_cost' => 'nullable|numeric|min:0',
            'min_price'          => 'nullable|numeric|min:0',
            'max_price'          => 'nullable|numeric|min:0',
            'high_cost'          => 'nullable',

            'paginate'           => 'nullable|integer|min:5|max:100',
            'order_by'           => ['nullable', Rule::in(['ASC', 'DESC', 'asc', 'desc'])],
            'sort_by'            => ['nullable', Rule::in(['id', 'name', 'price', 'created_at'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'search'             => __('product.index.request.attributes.search'),
            'currency_id'        => __('product.index.request.attributes.currency_id'),
            'manufacturing_cost' => __('product.index.request.attributes.manufacturing_cost'),
            'min_price'          => __('product.index.request.attributes.min_price'),
            'max_price'          => __('product.index.request.attributes.max_price'),
            'high_cost'          => __('product.index.request.attributes.high_cost'),
            'paginate'           => __('product.index.request.attributes.paginate'),
            'order_by'            => __('product.index.request.attributes.order_by'),
            'sort_by'            => __('product.index.request.attributes.sort_by'),
        ];
    }

    public function messages(): array
    {
        return [
            'string'        =>  __('product.index.request.messages.string'),
            'numeric'       =>  __('product.index.request.messages.numeric'),
            'integer'       =>  __('product.index.request.messages.integer'),
            'min'    => [
                'numeric'   => __('product.index.request.messages.min.numeric'),
                'string'    => __('product.index.request.messages.min.string'),
            ],
            'max'    => [
                'numeric'  => __('product.index.request.messages.max.numeric'),
                'string'   => __('product.index.request.messages.max.string'),
            ],
            'exists'       => __('product.index.request.messages.exists'),
            'in'           => __('product.index.request.messages.in'),
            'search.max'   => __('product.index.request.messages.search.max'),
            'paginate.min' => __('product.index.request.messages.paginate.min'),
            'paginate.max' => __('product.index.request.messages.paginate.max'),
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        Log::error( $validator->errors()->toArray());
        throw new HttpResponseException(response()->json([
            'message' => __('product.destroy.message'),
            'value'   => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
    protected function failedAuthorization()
    {
        Log::warning( __('auth.request.authorization.message'));
        throw new HttpResponseException(response()->json([
            'message' => __('auth.request.authorization.message'),
            'value'   => [
                'authorization' => [__('auth.request.authorization.value')]
            ],
        ], Response::HTTP_FORBIDDEN));
    }
}
