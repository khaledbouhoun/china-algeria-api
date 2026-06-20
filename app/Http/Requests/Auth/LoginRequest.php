<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * The Firebase token is verified by the FirebaseAuthenticate middleware.
     * No additional body fields are required for login, but we allow the
     * request through unconditionally.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Login is driven entirely by the Firebase token in the Authorization
     * header. No body fields are mandatory, so the rules array is empty.
     * Override this method if you later want to accept optional metadata.
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
