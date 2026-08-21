<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use App\Models\OrderItemStep;
use App\Models\PackageItem;
use App\Models\PackageItemStep;
use App\Models\Status;
use App\Models\User;
use App\Services\RecuperationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class RecuperationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('package_item_steps');
        Schema::dropIfExists('order_item_steps');
        Schema::dropIfExists('package_items');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('statuses');
        Schema::dropIfExists('users');

        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('type')->default('PACKAGE_ITEM');
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code')->nullable();
            $table->integer('items_count')->default(0);
            $table->decimal('weight', 10, 3)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->unsignedBigInteger('current_step_id')->nullable();
            $table->timestamps();
        });

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

        Schema::create('order_item_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
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

        Schema::create('package_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->default(1);
            $table->unsignedBigInteger('order_item_id');
            $table->integer('quantity_allocated')->nullable();
            $table->decimal('weight_total_allocated', 10, 3)->default(0);
            $table->decimal('amount_total_allocated', 14, 2)->default(0);
            $table->unsignedBigInteger('current_step_id')->nullable();
            $table->integer('quantity_recupered')->nullable();
            $table->decimal('weight_total_recupered', 10, 3)->default(0);
            $table->decimal('amount_total_recupered', 14, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    private function makeUser(int $roleId, int $id = 99): User
    {
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

    private function makeConfirmedDiscretePackageItem(int $qty = 5, float $unitPrice = 20.0, float $unitWeight = 1.5): PackageItem
    {
        $orderItem = OrderItem::create([
            'order_id'             => 1,
            'designation'          => 'Laptops',
            'quantity_declared'    => 10,
            'price_unit_declared'  => $unitPrice,
            'weight_unit_declared' => $unitWeight,
            'weight_total'         => 15.000,
            'current_step_id'      => null,
        ]);

        $orderStep = OrderItemStep::create([
            'item_id'   => $orderItem->id,
            'status_id' => Status::ITEM_A_RECEIVED,
            'zone_id'   => 8,
            'user_id'   => 1,
        ]);
        $orderItem->update(['current_step_id' => $orderStep->id]);

        $packageItem = PackageItem::create([
            'package_id'             => 1,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => $qty,
            'weight_total_allocated' => $qty * $unitWeight,
            'amount_total_allocated' => $qty * $unitPrice,
            'current_step_id'        => null,
        ]);

        $step = PackageItemStep::create([
            'package_item_id' => $packageItem->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
            'zone_id'         => 8,
            'user_id'         => 1,
        ]);
        $packageItem->update(['current_step_id' => $step->id]);

        return $packageItem;
    }

    private function makeConfirmedBulkPackageItem(float $allocatedWeight = 10.000, float $unitPrice = 5.0): PackageItem
    {
        $orderItem = OrderItem::create([
            'order_id'             => 1,
            'designation'          => 'Bulk Spices',
            'quantity_declared'    => null,
            'price_unit_declared'  => $unitPrice,
            'weight_unit_declared' => null,
            'weight_total'         => 20.000,
            'current_step_id'      => null,
        ]);

        $orderStep = OrderItemStep::create([
            'item_id'   => $orderItem->id,
            'status_id' => Status::ITEM_A_RECEIVED,
            'zone_id'   => 8,
            'user_id'   => 1,
        ]);
        $orderItem->update(['current_step_id' => $orderStep->id]);

        $packageItem = PackageItem::create([
            'package_id'             => 1,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => null,
            'weight_total_allocated' => $allocatedWeight,
            'amount_total_allocated' => $allocatedWeight * $unitPrice,
            'current_step_id'        => null,
        ]);

        $step = PackageItemStep::create([
            'package_item_id' => $packageItem->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
            'zone_id'         => 8,
            'user_id'         => 1,
        ]);
        $packageItem->update(['current_step_id' => $step->id]);

        return $packageItem;
    }

    public function test_agent_c_can_recuperate_discrete_items(): void
    {
        $packageItem = $this->makeConfirmedDiscretePackageItem(5, 20.0, 1.5);
        $agent = $this->makeUser(User::ROLE_AGENT_C);
        $service = new RecuperationService();

        $results = $service->recuperate($agent, [
            'items' => [
                [
                    'package_item_id'        => $packageItem->id,
                    'quantity_recupered'     => 5,
                    'weight_total_recupered' => 7.500,
                    'amount_total_recupered' => 100.00,
                    'comment'                => 'Handed to client',
                ],
            ],
        ]);

        $this->assertCount(1, $results);
        $recuperated = $results[0];

        $this->assertSame(5, $recuperated->quantity_recupered);
        $this->assertSame('7.500', (string) $recuperated->weight_total_recupered);
        $this->assertSame('100.00', (string) $recuperated->amount_total_recupered);
        $this->assertSame(Status::PACKAGE_ITEM_CL_RECEIVED, $recuperated->currentStep->status_id);
    }

    public function test_cashier_can_recuperate_bulk_items(): void
    {
        $packageItem = $this->makeConfirmedBulkPackageItem(10.000, 5.0);
        $cashier = $this->makeUser(User::ROLE_CASHIER);
        $service = new RecuperationService();

        $results = $service->recuperate($cashier, [
            'items' => [
                [
                    'package_item_id'        => $packageItem->id,
                    'quantity_recupered'     => null,
                    'weight_total_recupered' => 10.000,
                    'amount_total_recupered' => 50.00,
                ],
            ],
        ]);

        $this->assertCount(1, $results);
        $recuperated = $results[0];

        $this->assertNull($recuperated->quantity_recupered);
        $this->assertSame('10.000', (string) $recuperated->weight_total_recupered);
        $this->assertSame('50.00', (string) $recuperated->amount_total_recupered);
        $this->assertSame(Status::PACKAGE_ITEM_CL_RECEIVED, $recuperated->currentStep->status_id);
    }

    public function test_unauthorized_roles_are_forbidden(): void
    {
        $packageItem = $this->makeConfirmedDiscretePackageItem();
        $gladiator = $this->makeUser(User::ROLE_GLADIATOR);
        $service = new RecuperationService();

        $this->expectException(AccessDeniedHttpException::class);

        $service->recuperate($gladiator, [
            'items' => [
                [
                    'package_item_id'    => $packageItem->id,
                    'quantity_recupered' => 5,
                ],
            ],
        ]);
    }

    public function test_rejection_if_item_not_in_cl_confirmed_status(): void
    {
        $packageItem = $this->makeConfirmedDiscretePackageItem();
        // Change status to in-transit (PACKAGE_ITEM_SHIPPED)
        $step = PackageItemStep::create([
            'package_item_id' => $packageItem->id,
            'status_id'       => Status::PACKAGE_ITEM_SHIPPED,
            'zone_id'         => 8,
            'user_id'         => 1,
        ]);
        $packageItem->update(['current_step_id' => $step->id]);

        $agent = $this->makeUser(User::ROLE_AGENT_C);
        $service = new RecuperationService();

        $this->expectException(ValidationException::class);

        $service->recuperate($agent, [
            'items' => [
                [
                    'package_item_id'    => $packageItem->id,
                    'quantity_recupered' => 5,
                ],
            ],
        ]);
    }

    public function test_rejection_if_already_recuperated(): void
    {
        $packageItem = $this->makeConfirmedDiscretePackageItem();
        // Change status to already DELIVERED/RECEIVED
        $step = PackageItemStep::create([
            'package_item_id' => $packageItem->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_RECEIVED,
            'zone_id'         => 8,
            'user_id'         => 1,
        ]);
        $packageItem->update(['current_step_id' => $step->id]);

        $agent = $this->makeUser(User::ROLE_AGENT_C);
        $service = new RecuperationService();

        $this->expectException(ValidationException::class);

        $service->recuperate($agent, [
            'items' => [
                [
                    'package_item_id'    => $packageItem->id,
                    'quantity_recupered' => 5,
                ],
            ],
        ]);
    }

    public function test_order_item_master_status_updates_to_fulfilled_when_all_packages_recuperated(): void
    {
        // Setup OrderItem with 10 units split into two packages of 5 units each
        $orderItem = OrderItem::create([
            'order_id'             => 1,
            'designation'          => 'Discrete 10 items',
            'quantity_declared'    => 10,
            'price_unit_declared'  => 10.00,
            'weight_unit_declared' => 1.0,
            'weight_total'         => 10.000,
            'current_step_id'      => null,
        ]);

        $orderStep = OrderItemStep::create([
            'item_id'   => $orderItem->id,
            'status_id' => Status::ITEM_A_RECEIVED,
            'zone_id'   => 8,
            'user_id'   => 1,
        ]);
        $orderItem->update(['current_step_id' => $orderStep->id]);

        // Package 1 (5 units)
        $pkgItem1 = PackageItem::create([
            'package_id'             => 1,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => 5,
            'weight_total_allocated' => 5.000,
            'amount_total_allocated' => 50.00,
        ]);
        $step1 = PackageItemStep::create([
            'package_item_id' => $pkgItem1->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
        ]);
        $pkgItem1->update(['current_step_id' => $step1->id]);

        // Package 2 (5 units)
        $pkgItem2 = PackageItem::create([
            'package_id'             => 2,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => 5,
            'weight_total_allocated' => 5.000,
            'amount_total_allocated' => 50.00,
        ]);
        $step2 = PackageItemStep::create([
            'package_item_id' => $pkgItem2->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
        ]);
        $pkgItem2->update(['current_step_id' => $step2->id]);

        $agent = $this->makeUser(User::ROLE_AGENT_C);
        $service = new RecuperationService();

        // 1st recuperation: Only Package 1 is collected (5 / 10)
        $service->recuperate($agent, [
            'items' => [
                [
                    'package_item_id'    => $pkgItem1->id,
                    'quantity_recupered' => 5,
                ],
            ],
        ]);

        $orderItem->refresh();
        // OrderItem should NOT be fulfilled yet (still at ITEM_A_RECEIVED)
        $this->assertSame(Status::ITEM_A_RECEIVED, $orderItem->currentStep->status_id);

        // 2nd recuperation: Package 2 is collected (remaining 5 / 10)
        $service->recuperate($agent, [
            'items' => [
                [
                    'package_item_id'    => $pkgItem2->id,
                    'quantity_recupered' => 5,
                ],
            ],
        ]);

        $orderItem->refresh();
        // Now total recupered is 5 + 5 = 10 == 10, OrderItem MUST be fulfilled (ITEM_CL_FULFILLED)
        $this->assertSame(Status::ITEM_CL_FULFILLED, $orderItem->currentStep->status_id);
    }

    public function test_bulk_order_item_master_status_updates_to_fulfilled(): void
    {
        $orderItem = OrderItem::create([
            'order_id'             => 1,
            'designation'          => 'Bulk 20kg Spices',
            'quantity_declared'    => null,
            'price_unit_declared'  => 5.00,
            'weight_unit_declared' => null,
            'weight_total'         => 20.000,
            'current_step_id'      => null,
        ]);

        $orderStep = OrderItemStep::create([
            'item_id'   => $orderItem->id,
            'status_id' => Status::ITEM_A_RECEIVED,
            'zone_id'   => 8,
            'user_id'   => 1,
        ]);
        $orderItem->update(['current_step_id' => $orderStep->id]);

        $pkgItem1 = PackageItem::create([
            'package_id'             => 1,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => null,
            'weight_total_allocated' => 12.000,
            'amount_total_allocated' => 60.00,
        ]);
        $step1 = PackageItemStep::create([
            'package_item_id' => $pkgItem1->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
        ]);
        $pkgItem1->update(['current_step_id' => $step1->id]);

        $pkgItem2 = PackageItem::create([
            'package_id'             => 2,
            'order_item_id'          => $orderItem->id,
            'quantity_allocated'     => null,
            'weight_total_allocated' => 8.000,
            'amount_total_allocated' => 40.00,
        ]);
        $step2 = PackageItemStep::create([
            'package_item_id' => $pkgItem2->id,
            'status_id'       => Status::PACKAGE_ITEM_CL_CONFIRMED,
        ]);
        $pkgItem2->update(['current_step_id' => $step2->id]);

        $cashier = $this->makeUser(User::ROLE_CASHIER);
        $service = new RecuperationService();

        // Batch recuperate both packages at once
        $results = $service->recuperate($cashier, [
            'items' => [
                [
                    'package_item_id'        => $pkgItem1->id,
                    'quantity_recupered'     => null,
                    'weight_total_recupered' => 12.000,
                ],
                [
                    'package_item_id'        => $pkgItem2->id,
                    'quantity_recupered'     => null,
                    'weight_total_recupered' => 8.000,
                ],
            ],
        ]);

        $this->assertCount(2, $results);
        $orderItem->refresh();
        $this->assertSame(Status::ITEM_CL_FULFILLED, $orderItem->currentStep->status_id);
    }
}
