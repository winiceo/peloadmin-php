<?php



use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    /**
     * Run the seeder in packages.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->call(\SlimKit\PlusCheckIn\Seeds\DatabaseSeeder::class);
        $this->call(\SlimKit\Plus\Packages\News\Seeds\DatabaseSeeder::class);
        $this->call(\SlimKit\Plus\Packages\Feed\Seeds\DatabaseSeeder::class);
        $this->call(\Leven\Packages\Music\Seeds\DatabaseSeeder::class);
    }
}
