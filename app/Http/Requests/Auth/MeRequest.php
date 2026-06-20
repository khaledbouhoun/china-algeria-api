<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MeRequest extends FormRequest
{
    /**
     * The GET /me endpoint only reads the Firebase token from the Authorization
     * header (handled by FirebaseAuthenticate middleware). No request body
     * fields are needed.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * No body parameters are expected for the /me endpoint.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Return a consistent JSON error response instead of redirecting.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
