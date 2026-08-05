<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function __construct(private readonly UserService $service)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $user = $request->user();

    $items = $this->service->list($user, $request->all());

    return $this->success(UserResource::collection($items), 'Items retrieved successfully.');
  }

  public function show(Request $request, int $id): JsonResponse
  {
    $item = $this->service->find($request->user(), $id);

    return $this->success($item ? new UserResource($item) : null, 'Item retrieved successfully.');
  }

  public function store(StoreUserRequest $request): JsonResponse
  {
    $item = $this->service->create($request->user(), $request->validated());

    return $this->success(new UserResource($item), 'Item created successfully.', 201);
  }

  public function update(Request $request, int $id, UpdateUserRequest $formRequest): JsonResponse
  {
    $item = $this->service->update($request->user(), $id, $formRequest->validated());

    return $this->success(new UserResource($item), 'Item updated successfully.');
  }

  public function destroy(Request $request, int $id): JsonResponse
  {
    $this->service->delete($request->user(), $id);

    return $this->success(null, 'Item deleted successfully.');
  }
}
