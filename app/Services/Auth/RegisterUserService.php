<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterUserService
{
  /**
   * Maps role_id → short alphabetic prefix used in public_code generation.
   *
   * Format: USR-{PREFIX}-{sequential_number}
   * Example: USR-CLI-001, USR-ADM-003, USR-DLV-012
   */
  private const ROLE_PREFIX_MAP = [
    1 => 'ADM',   // Admin
    2 => 'HD',   // Client
    3 => 'CAS',   // Cashier
    4 => 'AG_A',  // Agent A
    5 => 'AG_C',  // Agent C
    6 => 'RS_A',  // Responsable A
    7 => 'RS_C',  // Responsable C
    8 => 'GLDR',  // Gladiator
    9 => 'DLV',   // Delivery
    10 => 'VRF',   // Verifier
  ];

  // -------------------------------------------------------------------------
  // [ADM , HD , VRF , GLDR] => 1
  // [AG_A , RS_A] => 2
  // [CAS , AG_C , RS_C , DLV] => 4
  // -------------------------------------------------------------------------
  private const ZONE_ROLE_PREFIX_MAP = [
    1 => 1,   // Admin
    2 => 1,   // Client
    3 => 4,   // Cashier
    4 => 2,  // Agent A
    5 => 4,  // Agent C
    6 => 2,  // Responsable A
    7 => 4,  // Responsable C
    8 => 1,  // Gladiator
    9 => 4,   // Delivery
    10 => 1,   // Verifier
  ];



  /**
   * Register a new user.
   *
   * @param  array{uid: string, email: string, email_verified: bool}  $firebaseUser
   * @param  array{full_name: string, phone: ?string, address: ?string, role_id: int, zone_id: ?int}  $data
   *
   * @throws \Illuminate\Validation\ValidationException  When the firebase_uid or email is already taken.
   */

  public function execute(array $firebaseUser, array $data): User
  {
    $this->guardAgainstDuplicateFirebaseUid($firebaseUser['uid']);
    $this->guardAgainstDuplicateEmail($firebaseUser['email']);

    try {


      $publicCode = $this->generatePublicCode((int) $data['role_id']);
      $zoneId = $this->setZone((int) $data['role_id']);

      $user = User::create([
        'public_code' => $publicCode,
        'full_name' => $data['full_name'],
        'email' => $firebaseUser['email'],
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'firebase_uid' => $firebaseUser['uid'],
        'role_id' => $data['role_id'],
        'zone_id' => $zoneId,
        'status' => 'PENDING',
        'proved_at' => null,
      ]);


      // return $user;
      return $user->load('role', 'zone');
    } catch (\Throwable $e) {
      dd(
        $e->getMessage(),
        $e->getTraceAsString()
      );
    }
  }



  // -------------------------------------------------------------------------
  // Private helpers
  // -------------------------------------------------------------------------

  /**
   * Abort with 409 if the firebase_uid is already registered.
   */
  private function guardAgainstDuplicateFirebaseUid(string $uid): void
  {
    if (User::where('firebase_uid', $uid)->exists()) {
      abort(response()->json([
        'status' => 'error',
        'message' => 'An account with this Firebase UID already exists.',
      ], 409));
    }
  }

  /**
   * Abort with 409 if the email is already registered.
   */
  private function guardAgainstDuplicateEmail(string $email): void
  {
    if (User::where('email', $email)->exists()) {
      abort(response()->json([
        'status' => 'error',
        'message' => 'An account with this email address already exists.',
      ], 409));
    }
  }

  /**
   * Generate the next sequential public_code for the given role.
   *
   * The sequence is derived from the highest numeric suffix found among
   * existing users with the same role_id, then incremented by 1.
   *
   * Examples:
   *   No existing user  → USR-CLI-1
   *   Last code USR-CLI-5 → USR-CLI-6
   */
  private function generatePublicCode(int $roleId): string
  {
    $prefix = self::ROLE_PREFIX_MAP[$roleId] ?? 'USR';

    $lastUser = User::where('role_id', $roleId)
      ->whereNotNull('public_code')
      ->orderByDesc('id')
      ->first();

    $nextNumber = 1;

    if ($lastUser && $lastUser->public_code) {
      preg_match('/(\d+)$/', $lastUser->public_code, $matches);
      $nextNumber = !empty($matches[1]) ? ((int) $matches[1] + 1) : 1;
    }

    // Zero-pad to at least 8 digits: 00000001, 00000002, ..., 00000100, 00000101
    $paddedNumber = str_pad((string) $nextNumber, 8, '0', STR_PAD_LEFT);

    return "{$prefix}-{$paddedNumber}";
  }
  private function setZone(int $roleId): string
  {
    $zoneId = self::ZONE_ROLE_PREFIX_MAP[$roleId] ?? 1;
    return $zoneId;
  }
}
