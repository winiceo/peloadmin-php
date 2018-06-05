<?php



use Illuminate\Database\Seeder;
use Leven\Models\CurrencyType;

class CurrencyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = CurrencyType::where('name', '积分')->count();
        if (! $count) {
            CurrencyType::create(['name' => '积分', 'unit' => '', 'enable' => 1]);
        }
    }
}
