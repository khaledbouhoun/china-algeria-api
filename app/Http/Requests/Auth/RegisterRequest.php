<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
  /**
   * All auth routes are guarded by FirebaseAuthenticate middleware,
   * so by the time this request is resolved the token has already been
   * verified — we can safely allow all users through here.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Validation rules for the registration payload.
   *
   * The Firebase token is handled by the middleware;
   * this request only validates the business fields sent in the body.
   */
  public function rules(): array
  {
    return [
      'full_name' => ['required', 'string', 'max:255'],
      'phone'     => ['nullable', 'string', 'max:50'],
      'address'   => ['nullable', 'string', 'max:500'],
      'role_id'   => ['required', 'integer', 'min:1', 'max:10', 'exists:roles,id'],
      // 'zone_id'   => ['nullable', 'integer', 'exists:zones,id'],
    ];
  }

  /**
   * Human-readable attribute names for error messages.
   */
  public function attributes(): array
  {
    return [
      'full_name' => 'full name',
      'phone'     => 'phone number',
      'address'   => 'address',
      'role_id'   => 'role',
      'zone_id'   => 'zone',
    ];
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
