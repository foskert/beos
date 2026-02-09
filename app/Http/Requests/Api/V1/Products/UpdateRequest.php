<?php

namespace App\Http\Requests\Api\V1\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
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
        return  $this->user()->can('update', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId)
            ],
            'description'        => ['nullable', 'string'],
            'price'              => ['sometimes', 'numeric', 'min:0'],
            'tax_cost'           => ['sometimes', 'numeric', 'min:0'],
            'currency_id'        => ['sometimes', 'exists:currencies,id'],
            'manufacturing_cost' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
    public function attributes(): array
    {
        return [
            'name'               => __('product.update.request.attributes.name'),
            'description'        => __('product.update.request.attributes.description'),
            'currency_id'        => __('product.update.request.attributes.currency_id'),
            'price'              => __('product.update.request.attributes.price'),
            'tax_cost'           => __('product.update.request.attributes.tax_cost'),
            'manufacturing_cost' => __('product.update.request.attributes.manufacturing_cost'),
        ];
    }
    public function messages(): array
    {
        return [
            'string'        =>  __('product.update.request.messages.string'),
            'numeric'       =>  __('product.update.request.messages.numeric'),
            'integer'       =>  __('product.update.request.messages.integer'),
            'required'      =>  __('product.update.request.messages.required'),
            'max'           =>  __('product.update.request.messages.max'),
            'min'           =>  __('product.update.request.messages.min'),
            'exists'        =>  __('product.update.request.messages.exists'),
            'in'            =>  __('product.update.request.messages.name.in'),
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        Log::error( $validator->errors()->toArray());
        throw new HttpResponseException(response()->json([
            'message' => __('product.store.errors'),
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
