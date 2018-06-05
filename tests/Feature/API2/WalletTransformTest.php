<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WalletTransformTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
        $this->user->newWallet()->create(['balance' => 999999, 'total_income' => 0, 'total_expenses' => 0]);
    }

    /**
     * 测试发起转换积分.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testTransfer()
    {
        $response = $this->actingAs($this->user, 'api')->json('POST', '/api/v2/plus-pay/transform', ['amount' => 2121]);

        $response->assertStatus(201);
    }

    protected function tearDown()
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}
