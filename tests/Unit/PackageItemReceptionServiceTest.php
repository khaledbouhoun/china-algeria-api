<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use App\Models\PackageItem;
use App\Models\PackageItemReception;
use App\Models\User;
use App\Services\PackageItemReceptionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class PackageItemReceptionServiceTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::dropIfExists('package_item_receptions');
    Schema::dropIfExists('package_item_steps');
    Schema::dropIfExists('package_items');
    Schema::dropIfExists('order_items');
    Schema::dropIfExists('users');

    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('public_code')->nullable();
      $table->string('full_name')->nullable();
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->string('address')->nullable();
      $table->string('firebase_uid')->nullable();
      $table->unsignedBigInteger('role_id')->nullable();
      $table->unsignedBigInteger('zone_id')->nullable();
      $table->string('status')->default('ENABLED');
      $table->timestamp('proved_at')->nullable();
      $table->timestamp('last_login_at')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id')->default(1);
      $table->string('designation');
      $table->integer('quantity_declared')->nullable();
      $table->decimal('price_unit_declared', 14, 2)->default(0);
      $table->decimal('weight_unit_declared', 10, 3)->nullable();
      $table->decimal('weight_total', 10, 3)->default(0);
      $table->unsignedBigInteger('current_step_id')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('package_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('package_id')->default(1);
      $table->unsignedBigInteger('order_item_id');
      $table->integer('quantity_allocated')->nullable();
      $table->decimal('weight_total_allocated', 10, 3)->default(0);
      $table->decimal('amount_total_allocated', 14, 2)->default(0);
      $table->unsignedBigInteger('current_step_id')->nullable();
      $table->integer('quantity_recupered')->default(0);
      $table->decimal('weight_total_recupered', 10, 3)->default(0);
      $table->decimal('amount_total_recupered', 14, 2)->default(0);
      $table->integer('created_by')->nullable();
      $table->integer('updated_by')->nullable();
      $table->timestamps();
    });

    Schema::create('package_item_steps', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('package_item_id');
      $table->unsignedBigInteger('status_id');
      $table->unsignedBigInteger('zone_id')->nullable();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->text('comment')->nullable();
      $table->unsignedBigInteger('created_by')->nullable();
      $table->timestamps();
    });

    Schema::create('package_item_receptions', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('package_item_id');
      $table->unsignedBigInteger('inspected_by');
      $table->integer('expected_quantity')->nullable();
      $table->decimal('expected_weight', 10, 3)->default(0);
      $table->integer('received_quantity')->nullable();
      $table->decimal('received_weight', 10, 3)->default(0);
      $table->integer('difference_quantity')->nullable();
      $table->decimal('difference_weight', 10, 3)->default(0);
      $table->integer('count_reception')->default(0);
      $table->text('comment')->nullable();
      $table->timestamps();
    });
  }

  // ─── Helper ────────────────────────────────────────────────────────

  private function makeUser(int $roleId, int $id = 99): User
  {
    // Persist to DB so ->load(['inspector']) can find the user.
    $existing = User::find($id);
    if ($existing) {
      return $existing;
    }

    $user = new User();
    $user->id = $id;
    $user->role_id = $roleId;
    $user->zone_id = 8;
    $user->full_name = 'Test User ' . $id;
    $user->email = 'user' . $id . '@test.com';
    $user->status = User::STATUS_ENABLED;
    $user->save();

    return $user;
  }

  private function makeDiscretePackageItem(): PackageItem
  {
    $orderItem = OrderItem::create([
      'order_id' => 1,
      'designation' => 'Discrete item',
      'quantity_declared' => 10,
      'price_unit_declared' => 10.00,
      'weight_unit_declared' => 1.5,
      'weight_total' => 15.000,
      'current_step_id' => 1,
    ]);

    return PackageItem::create([
      'package_id' => 1,
      'order_item_id' => $orderItem->id,
      'quantity_allocated' => 5,
      'weight_total_allocated' => 7.500,
      'amount_total_allocated' => 50.00,
    ]);
  }

  private function makeBulkPackageItem(): PackageItem
  {
    $orderItem = OrderItem::create([
      'order_id' => 1,
      'designation' => 'Bulk item',
      'quantity_declared' => null,
      'price_unit_declared' => 0.00,
      'weight_unit_declared' => null,
      'weight_total' => 20.000,
      'current_step_id' => 1,
    ]);

    return PackageItem::create([
      'package_id' => 1,
      'order_item_id' => $orderItem->id,
      'quantity_allocated' => null,
      'weight_total_allocated' => 12.000,
      'amount_total_allocated' => 0.00,
    ]);
  }

  // ─── Discrete Item Tests ──────────────────────────────────────────

  public function test_discrete_item_reception_by_agent(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $user = $this->makeUser(User::ROLE_AGENT_C);
    $service = new PackageItemReceptionService();

    $reception = $service->create($user, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
      'comment' => 'Missing one piece',
    ]);

    $this->assertSame(5, $reception->expected_quantity);
    $this->assertSame(7.500, (float) $reception->expected_weight);
    $this->assertSame(4, $reception->received_quantity);
    $this->assertSame(6.000, (float) $reception->received_weight);
    $this->assertSame(-1, $reception->difference_quantity);
    $this->assertSame(-1.500, (float) $reception->difference_weight);
    $this->assertSame(1, $reception->count_reception);
  }

  // ─── Bulk Item Tests ──────────────────────────────────────────────

  public function test_bulk_item_reception_by_verifier(): void
  {
    $packageItem = $this->makeBulkPackageItem();
    $user = $this->makeUser(User::ROLE_VERIFIER);
    $service = new PackageItemReceptionService();

    $reception = $service->create($user, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => null,
      'received_weight' => 11.500,
      'comment' => 'Slight weight discrepancy',
    ]);

    $this->assertNull($reception->expected_quantity);
    $this->assertSame(12.000, (float) $reception->expected_weight);
    $this->assertNull($reception->received_quantity);
    $this->assertSame(11.500, (float) $reception->received_weight);
    $this->assertNull($reception->difference_quantity);
    $this->assertSame(-0.500, (float) $reception->difference_weight);
    $this->assertSame(1, $reception->count_reception);
  }

  // ─── Status Progression Tests ─────────────────────────────────────

  public function test_creates_cl_confirmed_step_and_updates_package_item(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $user = $this->makeUser(User::ROLE_AGENT_C);
    $service = new PackageItemReceptionService();

    $service->create($user, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 5,
      'received_weight' => 7.500,
    ]);

    $packageItem->refresh();

    // The PackageItem should have a new current_step_id
    $this->assertNotNull($packageItem->current_step_id);

    // The step should have CL_CONFIRMED status (11)
    $latestStep = $packageItem->steps()->first();
    $this->assertNotNull($latestStep);
    $this->assertSame(11, $latestStep->status_id);  // Status::PACKAGE_ITEM_CL_CONFIRMED
    $this->assertSame(99, $latestStep->user_id);
  }

  // ─── 3-Strike Escalation Tests ────────────────────────────────────

  public function test_attempt_counter_increments(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $agent = $this->makeUser(User::ROLE_AGENT_C);
    $service = new PackageItemReceptionService();

    $r1 = $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);

    $r2 = $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.500,
    ]);

    $this->assertSame(1, $r1->count_reception);
    $this->assertSame(2, $r2->count_reception);
  }

  public function test_attempt_3_requires_supervisor(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $agent = $this->makeUser(User::ROLE_AGENT_C, 99);
    $supervisor = $this->makeUser(User::ROLE_RESPONSABLE_C, 100);
    $service = new PackageItemReceptionService();

    // Attempts 1 & 2 by agent
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);

    // Attempt 3 by agent should be denied
    $this->expectException(AccessDeniedHttpException::class);

    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 5,
      'received_weight' => 7.500,
    ]);
  }

  public function test_attempt_3_succeeds_for_supervisor(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $agent = $this->makeUser(User::ROLE_AGENT_C, 99);
    $supervisor = $this->makeUser(User::ROLE_RESPONSABLE_C, 100);
    $service = new PackageItemReceptionService();

    // Attempts 1 & 2 by agent
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);

    // Attempt 3 by supervisor should succeed
    $r3 = $service->create($supervisor, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 5,
      'received_weight' => 7.500,
    ]);

    $this->assertSame(3, $r3->count_reception);
  }

  public function test_lockout_after_3_attempts(): void
  {
    $packageItem = $this->makeDiscretePackageItem();
    $agent = $this->makeUser(User::ROLE_AGENT_C, 99);
    $supervisor = $this->makeUser(User::ROLE_RESPONSABLE_C, 100);
    $service = new PackageItemReceptionService();

    // Exhaust all 3 attempts
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);
    $service->create($agent, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 4,
      'received_weight' => 6.000,
    ]);
    $service->create($supervisor, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 5,
      'received_weight' => 7.500,
    ]);

    // Attempt 4 by anyone should be locked out
    $this->expectException(ValidationException::class);

    $service->create($supervisor, [
      'package_item_id' => $packageItem->id,
      'received_quantity' => 5,
      'received_weight' => 7.500,
    ]);
  }

  // ─── Batch Tests ──────────────────────────────────────────────────

  public function test_batch_creates_multiple_receptions_atomically(): void
  {
    $discreteItem = $this->makeDiscretePackageItem();
    $bulkItem = $this->makeBulkPackageItem();
    $agent = $this->makeUser(User::ROLE_AGENT_C, 99);
    $service = new PackageItemReceptionService();

    $results = $service->createWithItems($agent, [
      'items' => [
        [
          'package_item_id' => $discreteItem->id,
          'received_quantity' => 4,
          'received_weight' => 6.000,
          'comment' => 'Missing one piece',
        ],
        [
          'package_item_id' => $bulkItem->id,
          'received_quantity' => null,
          'received_weight' => 11.500,
          'comment' => 'Slight weight difference',
        ],
      ],
    ]);

    $this->assertCount(2, $results);

    // Discrete item assertions
    $discrete = $results[0];
    $this->assertSame(5, $discrete->expected_quantity);
    $this->assertSame(4, $discrete->received_quantity);
    $this->assertSame(-1, $discrete->difference_quantity);
    $this->assertSame(1, $discrete->count_reception);

    // Bulk item assertions
    $bulk = $results[1];
    $this->assertNull($bulk->expected_quantity);
    $this->assertNull($bulk->received_quantity);
    $this->assertNull($bulk->difference_quantity);
    $this->assertSame(1, $bulk->count_reception);

    // Both PackageItems should have updated step
    $discreteItem->refresh();
    $bulkItem->refresh();
    $this->assertNotNull($discreteItem->current_step_id);
    $this->assertNotNull($bulkItem->current_step_id);
  }
}
