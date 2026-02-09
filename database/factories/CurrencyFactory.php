<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    private const curriencies = [
        ['name' => 'US Dollar',        'symbol' => 'USD', 'rate' => 1.00],
        ['name' => 'Euro',             'symbol' => 'EUR', 'rate' => 0.92],
        ['name' => 'British Pound',    'symbol' => 'GBP', 'rate' => 0.79],
        ['name' => 'Japanese Yen',     'symbol' => 'JPY', 'rate' => 150.12],
        ['name' => 'Peso Mexicano',    'symbol' => 'MXN', 'rate' => 17.05],
        ['name' => 'Bolívar Soberano', 'symbol' => 'VES', 'rate' => 36.21],
        ['name' => 'Real Brasileño',   'symbol' => 'BRL', 'rate' => 4.95],
        ['name' => 'Canadian Dollar',  'symbol' => 'CAD', 'rate' => 1.35],
        ['name' => 'Swiss Franc',      'symbol' => 'CHF', 'rate' => 0.88],
        ['name' => 'Chinese Yuan',     'symbol' => 'CNY', 'rate' => 7.19],
    ];
    private static int $index = 0;

    public function definition(): array
    {
        $data = self::curriencies[self::$index % count(self::curriencies)];
        self::$index++;

        return [
            'name'          => $data['name'],
            'symbol'        => $data['symbol'],
            'exchange_rate' => $data['rate'],
        ];
    }
    public static function createFullSet()
    {
        return (new self())->count(count(self::curriencies))->create();
    }
}
