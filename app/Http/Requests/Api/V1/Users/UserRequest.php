<?php

namespace App\Http\Requests\Api\V1\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Cache\RateLimiter;
use Illuminate\Auth\Events\Lockout;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'max:255', 'email'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }
    public function attributes(): array
    {
        return [
            'email'    => __('user.request.attributes.email'),
            'password' => __('user.request.attributes.password'),
        ];
    }
     public function messages(): array
    {
        return [
            'string'        =>  __('user.request.messages.string'),
            'required'      =>  __('user.request.messages.required'),
            'max'           =>  __('user.request.messages.max'),
            'email'         =>  __('user.request.messages.email'),

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        Log::error( $validator->errors()->toArray());
        throw new HttpResponseException(response()->json([
            'message' => __('user.request.errors.message'),
            'value'   => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
    public function ensureIsNotRateLimited()
    {
        $key = Str::lower($this->input('email')).'|'.$this->ip();
        if (! app(RateLimiter::class)->tooManyAttempts($key, config('api.defaults.maxAttempts', 5))) {
            return;
        }
        event(new Lockout($this));
        throw new HttpResponseException(response()->json([
            'message' => __('user.request.authorization.error'),
        ], Response::HTTP_TOO_MANY_REQUESTS));
    }
}
