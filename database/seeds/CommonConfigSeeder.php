<?php



use Illuminate\Database\Seeder;
use Leven\Models\CommonConfig;

class CommonConfigSeeder extends Seeder
{
    /**
     * 添加注册用户的默认用户组.
     *
     * @return void
     */
    public function run()
    {
        CommonConfig::create([
            'name' => 'default_role',
            'namespace' => 'user',
            'value' => 2,
        ]);
    }
}
