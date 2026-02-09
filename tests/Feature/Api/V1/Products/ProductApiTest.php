<?php

namespace Tests\Feature\Api\V1\Products;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    private $user;
    private $password = 'password123';
    protected string $endpoint = '/api/v1/products';
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
    public function it_can_list_products(): void
    {
        Sanctum::actingAs($this->user);
        Product::factory()->count(3)->create([
            'manufacturing_cost' => 10.00,
            'tax_cost' => 2.00
        ]);
        $response = $this->getJson($this->endpoint);

        //$response->dump();
        $response->assertStatus(Response::HTTP_OK)
           ->assertJsonCount(3, 'value');
    }

    #[Test]
    public function it_can_create_a_product(): void
    {
        Sanctum::actingAs($this->user);
        $currency = Product::factory()->create();
        $payload = [
            'name' => 'New Product',
            'description' => 'Product Description',
            'price' => 150.00,
            'currency_id' => $currency->id,
            'tax_cost' => 10.00,
            'manufacturing_cost' => 50.00,
        ];

        $response = $this->postJson($this->endpoint, $payload);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['message' => __('product.store.message')]);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }


#[Test]
public function it_can_show_a_product(): void
{
    Sanctum::actingAs($this->user);
    $product = Product::factory()->create();
    $response = $this->getJson("{$this->endpoint}/{$product->id}");
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'value' => [
                'id',
                'name',
                'description',
                'price',
                'currency',
                'manufacturing_cost',
                'tax_cost',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('value.id', $product->id);
    }
    #[Test]
    public function it_can_delete_a_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$product->id}");
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSoftDeleted($product);
        $this->assertNull(Product::find($product->id));
    }
    #[Test]
    public function it_can_update_a_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create([
            'name' => 'Original Name'
        ]);
        $payload = [
            'name'               => 'Updated Name',
            'description'        => 'Updated Description',
            'price'              => 200.00,
            'currency_id'        => $product->currency_id,
            'tax_cost'           => 10.00,
            'manufacturing_cost' => 50.00,
        ];
        $response = $this->putJson("{$this->endpoint}/{$product->id}", $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('message', __('product.update.message'))
            ->assertJsonPath('value.name', 'Updated Name');
        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Updated Name',
            'price' => 200.00
        ]);
    }


}
