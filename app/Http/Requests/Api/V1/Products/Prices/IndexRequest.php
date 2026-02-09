<?php

namespace App\Http\Requests\Api\V1\Products\Prices;

use App\Models\ProductPrice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        return  $this->user()->can('show', ProductPrice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

        ];
    }
        public function attributes(): array
    {
        return [

        ];
    }
    public function messages(): array
    {
        return [

        ];
    }
     protected function failedValidation(Validator $validator)
    {
        Log::error( $validator->errors()->toArray());
        throw new HttpResponseException(response()->json([
            'message' => __('price.index.message'),
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
