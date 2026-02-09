<?php

namespace App\Http\Requests\Api\V1\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StoreRequest extends FormRequest
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
        return  $this->user()->can('store', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255', 'unique:products,name'],
            'price'              => ['required', 'numeric', 'min:0'],
            'tax_cost'           => ['required', 'numeric', 'min:0'],
            'currency_id'        => ['required', 'exists:currencies,id'],
            'manufacturing_cost' => ['required', 'numeric', 'min:0'],
            'description'        => ['nullable', 'string'],
        ];
    }
    public function attributes(): array
    {
        return [
            'name'               => __('product.store.request.attributes.name'),
            'description'        => __('product.store.request.attributes.description'),
            'currency_id'        => __('product.store.request.attributes.currency_id'),
            'price'              => __('product.store.request.attributes.price'),
            'tax_cost'           => __('product.store.request.attributes.tax_cost'),
            'manufacturing_cost' => __('product.store.request.attributes.manufacturing_cost'),
        ];
    }
    public function messages(): array
    {
        return [
            'string'        =>  __('product.store.request.messages.string'),
            'numeric'       =>  __('product.store.request.messages.numeric'),
            'integer'       =>  __('product.store.request.messages.integer'),
            'required'      =>  __('product.store.request.messages.required'),
            'max'           =>  __('product.store.request.messages.max'),
            'min'           =>  __('product.store.request.messages.min'),
            'unique'        =>  __('product.store.request.messages.unique'),
            'exists'        =>  __('product.store.request.messages.exists'),
            'name.unique'   =>  __('product.store.request.messages.name.unique'),
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
