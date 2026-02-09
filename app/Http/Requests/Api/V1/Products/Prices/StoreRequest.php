<?php

namespace App\Http\Requests\Api\V1\Products\Prices;

use App\Models\ProductPrice;
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
        return  $this->user()->can('store', ProductPrice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price'              => ['required', 'numeric', 'min:0'],
            'product_id'         => ['required', 'exists:products,id'],
            'currency_id'        => ['required', 'exists:currencies,id'],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'product_id' => $this->route('id'),
        ]);
    }
    public function attributes(): array
    {
        return [
            'product_id'         => __('price.store.request.attributes.product_id'),
            'currency_id'        => __('price.store.request.attributes.currency_id'),
            'price'              => __('price.store.request.attributes.price'),
        ];
    }
    public function messages(): array
    {
        return [
            'numeric'            =>  __('product.store.request.messages.numeric'),
            'required'           =>  __('price.store.request.messages.required'),
            'min'                =>  __('price.store.request.messages.min'),
            'product_id.exists'  =>  __('price.store.request.messages.exists.product_id'),
            'currency_id.exists' =>  __('price.store.request.messages.exists.currency_id'),
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
