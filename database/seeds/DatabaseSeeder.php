<?php



use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CertificationCategoryTableSeeder::class); // 用户认证类型
        $this->call(AdvertisingSpaceTableSeeder::class); // 广告位类型
        $this->call(PackagesSeeder::class); // Packages seeder.
        $this->call(CurrencyTypeSeeder::class); // 默认的货币类型
        $this->call(CommonConfigSeeder::class); // 默认用户组
        // 把地区放在最后，因为耗时较长.
        $this->call(AreasTableSeeder::class);
    }
}
