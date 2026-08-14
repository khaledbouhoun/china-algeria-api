<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use App\Models\PackageItem;
use App\Models\User;
use App\Services\PackageItemService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PackageItemServiceTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::dropIfExists('package_item_steps');
    Schema::dropIfExists('package_items');
    Schema::dropIfExists('order_items');

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
      $table->integer('quantity_allocated')->default(0);
      $table->decimal('weight_total_allocated', 10, 3)->default(0);
      $table->decimal('amount_total_allocated', 14, 2)->default(0);
      $table->unsignedBigInteger('current_step_id')->nullable();
      $table->integer('quantity_recupered')->default(0);
      $table->decimal('weight_total_recupered', 10, 3)->default(0);
      $table->decimal('amount_total_recupered', 14, 2)->default(0);
      $table->unsignedBigInteger('created_by')->nullable();
      $table->unsignedBigInteger('updated_by')->nullable();
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
  }

  public function test_it_uses_weight_total_when_unit_fields_are_null(): void
  {
    $orderItem = OrderItem::create([
      'order_id' => 1,
      'designation' => 'Test item',
      'quantity_declared' => null,
      'price_unit_declared' => 12.50,
      'weight_unit_declared' => null,
      'weight_total' => 25.000,
      'current_step_id' => 1,
    ]);

    PackageItem::create([
      'package_id' => 1,
      'order_item_id' => $orderItem->id,
      'quantity_allocated' => 1,
      'weight_total_allocated' => 10.000,
      'amount_total_allocated' => 12.50,
      'current_step_id' => 1,
      'quantity_recupered' => 0,
      'weight_total_recupered' => 0,
      'amount_total_recupered' => 0,
    ]);

    $user = new User(['id' => 99, 'zone_id' => 8]);
    $service = new PackageItemService();

    $item = $service->create($user, [
      'package_id' => 1,
      'order_item_id' => $orderItem->id,
      'quantity_allocated' => 2,
      'weight_total_allocated' => 10.000,
      'amount_total_allocated' => 25.00,
    ]);

    $this->assertSame(2, $item->quantity_allocated);
    $this->assertSame('10.000', (string) $item->weight_total_allocated);
    $this->assertSame('25.00', (string) $item->amount_total_allocated);
  }
}
