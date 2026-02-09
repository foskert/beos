<?php

namespace Tests\Feature\Api\V1\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserApiTest extends TestCase
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

    }
    #[Test]
    public function _is_login_in()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);
        return  $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'value' => [
                    'access_token',
                    'token_type',
                    'user' => ['id', 'name', 'email']
                ]
            ]);
    }
    #[Test]
    public function _is_logged_out()
    {
        $this->withoutExceptionHandling();
        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/v1/logout',[]);
        return  $response->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
