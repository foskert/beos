<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Database\Factories\CurrencyFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedRolesAndPermissions();
        $this->seedUsers();
        $currencies = $this->seedCurrency();
        $this->seedProducts($currencies);
    }
    private function seedRolesAndPermissions(): void
{
    $guard = 'api';

    $permissions = [
        'products.destroy',
        'products.index',
        'products.show',
        'products.store',
        'products.update',
        'price.index',
        'price.store',
        'audit.index',
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => $guard
        ]);
    }

    $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
    $admin->syncPermissions(Permission::all());

    $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => $guard]);
    $user->syncPermissions([
        'products.index',
        'products.show',
        'price.index',
        'audit.index',
    ]);
}
    private function seedUsers(): void
    {
        $admin = User::updateOrCreate(
        ['email' => 'admin@admin.com'],
            [
                'name'              => 'Admin User',
                'email_verified_at' => now(),
                'password'          => Hash::make('1234567890'),
            ]
        );
        $admin->assignRole('admin');
        if (User::count() <= 1) {
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
    }
    private function seedCurrency()
    {
        return CurrencyFactory::createFullSet();
    }

    private function seedProducts($currencies): void
    {
        Product::factory()
            ->count(100)
            ->recycle($currencies)
            ->create()
            ->each(function (Product $product) use ($currencies) {
                $extraCurrencies = $currencies->random(rand(0, $currencies->count()));
                if ($extraCurrencies->isNotEmpty()) {
                    $prices = $extraCurrencies->map(fn($currency) => [
                        'currency_id' => $currency->id,
                        'price'       => $product->price * $currency->exchange_rate,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ])->toArray();
                    $product->regionalPrices()->createMany($prices);
                }
            });
    }
}
