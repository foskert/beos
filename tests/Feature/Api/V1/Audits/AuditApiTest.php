<?php

namespace Tests\Feature\Api\V1\Audits;

use App\Models\Audit;
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

class AuditApiTest extends TestCase
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
    public function it_can_list_audits_for_a_product(): void
    {
        Sanctum::actingAs($this->user);
        $product = Product::factory()->create();
        Audit::create([
            'event'          => 'created',
            'auditable_id'   => $product->id,
            'auditable_type' => Product::class,
            'user_id'        => $this->user->id,
            'old_values'     => null,
            'new_values'     => ['name' => $product->name],
            'url'            => 'http://localhost',
            'ip_address'     => '127.0.0.1'
        ]);

        $response = $this->getJson("/api/v1/audits/products/{$product->id}");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'value' => [
                    '*' => [
                        'id',
                        'event',
                        'user' => [
                            'id',
                            'name'
                        ],
                        'changes' => [
                            'before',
                            'after'
                        ],
                        'metadata' => [
                            'ip',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }
}
