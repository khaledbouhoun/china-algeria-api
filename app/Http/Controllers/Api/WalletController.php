<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Resources\Wallet\WalletResource;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
  public function __construct(private readonly WalletService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->userOrFail();
    $items = $this->service->list($user, $request->all());

    return response()->json([
      'success' => true,
      'message' => 'Items retrieved successfully.',
      'data' => WalletResource::collection($items),
    ]);
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->find($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item retrieved successfully.',
      'data' => $item ? new WalletResource($item) : null,
    ]);
  }

  public function store(StoreWalletRequest $request): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->create($user, $request->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item created successfully.',
      'data' => new WalletResource($item),
    ], 201);
  }

  public function update(Request $request, int $id, UpdateWalletRequest $formRequest): JsonResponse
  {
    $user = $request->userOrFail();
    $item = $this->service->update($user, $id, $formRequest->validated());

    return response()->json([
      'success' => true,
      'message' => 'Item updated successfully.',
      'data' => new WalletResource($item),
    ]);
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $user = $request->userOrFail();
    $this->service->delete($user, $id);

    return response()->json([
      'success' => true,
      'message' => 'Item deleted successfully.',
      'data' => null,
    ]);
  }
}
