<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class PortalLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required|string',
            'user_id' => 'required|integer'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        Log::info('------------------');
        Log::info('Method: ' . $this->method());
        Log::info('URL: ' . $this->fullUrl());

        $validator->after(function ($validator) {
            if (getHost($this->headers->get('referer')) !== getHost(config('app.portal_url'))) {
                $validator->errors()->add('referer_url', 'Invalid referer url.');
            }
            
            if ($validator->errors()->getMessages()) {
                Log::error($validator->errors());
                throw new AuthorizationException();
            }
        });
    }
}
