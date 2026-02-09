<?php

namespace Tests\Feature\Api\V1\Products\Prices;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductPriceApiTest extends TestCase
{
    use RefreshDatabase;
   private $user;
    private $password = 'password123';
    protected function setUp(): void
    {
         parent::setUp();

        Permission::create(['name' => 'products.destroy']);
        Permission::create(['name' => 'products.index']);
        Permission::create(['name' => 'products.show']);
        Permission::create(['name' => 'products.store']);
        Permission::create(['name' => 'products.update']);
        Permission::create(['name' => 'price.index']);
        Permission::create(['name' => 'price.store']);
        Permission::create(['name' => 'audit.index']);
        $admin =Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        $this->user = User::factory()->create([
            'password' => Hash::make($this->password),
            'email'    => 'admin@admin.com'
        ]);
        $this->user->assignRole('admin');
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);
    }
    #[Test]
    public function it_can_list_prices_for_a_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create();
        ProductPrice::factory()->count(3)->create([
            'product_id' => $product->id
        ]);
        $response = $this->getJson("/api/v1/products/{$product->id}/prices");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'value' => [
                    'data' => [ // Laravel Resources envuelve en 'data' por defecto
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'currency' => ['name', 'symbol', 'exchange_rate'],
                            'prices' => [
                                '*' => [
                                    'price',
                                    'currency' => ['name', 'symbol', 'exchange_rate']
                                ]
                            ],
                            'manufacturing_cost',
                            'tax_cost',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }

   #[Test]
public function it_can_create_a_price_for_a_product(): void
{
    Sanctum::actingAs($this->user);
    $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    $product = Product::factory()->create();
    $currency = Currency::factory()->create();
    $payload = [
        'price'       => 99.99,
        'currency_id' => $currency->id,
        'product_id'  => $product->id,
    ];
    $response = $this->postJson("/api/v1/products/{$product->id}/prices", $payload);
    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonFragment(['message' => __('price.store.message')]);
    $this->assertDatabaseHas('product_prices', [
        'product_id'  => $product->id,
        'price'       => 99.99,
        'currency_id' => $currency->id
    ]);
}

}
