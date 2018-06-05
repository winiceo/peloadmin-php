<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Leven\Repository\UserWalletCashType;
use Leven\Models\WalletCash as WalletCashModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WalletCashTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
        $this->user->newWallet()->create(['balance' => 999999, 'total_income' => 0, 'total_expenses' => 0]);

        factory(WalletCashModel::class)->create(['user_id' => $this->user->id]);
        factory(WalletCashModel::class)->create(['user_id' => $this->user->id]);
        factory(WalletCashModel::class)->create(['user_id' => $this->user->id]);
        app(UserWalletCashType::class)->store(['alipay']);
        app(UserWalletCashType::class)->flush();
    }

    /**
     * 测试获取提现记录.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetCashes()
    {
        $firstrResponse = $this->actingAs($this->user, 'api')->json('GET', '/api/v1/plus-pay/cashes?limit=1');

        $firstrResponse->assertStatus(200);

        $data = $firstrResponse->json()[0];
        $this->assertTrue(count($firstrResponse->json()) === 1);

        $this->assertOrderData($data);

        $after = $this->actingAs($this->user, 'api')->json('GET', '/api/v1/plus-pay/cashes?after='.$data['id']);

        $after->assertStatus(200);

        $afterData = $after->json()[0];

        $this->assertOrderData($afterData);
        $this->assertTrue(count($after->json()) === 2);
        $this->assertTrue($afterData['id'] < $data['id']);
    }

    /**
     * 测试发起提现.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testCreateCash()
    {
        $response = $this->actingAs($this->user, 'api')->json('post', '/api/v1/plus-pay/cashes', [
            'value' => 1234,
            'type' => 'alipay',
            'account' => 'asas@aaa.com',
        ]);

        $response->assertStatus(201);
    }

    /**
     * 断言提现数据基本结构.
     *
     * @param array $singleData
     * @return void
     * @author BS <414606094@qq.com>
     */
    protected function assertOrderData(array $singleData)
    {
        $this->assertArrayHasKey('id', $singleData);
        $this->assertArrayHasKey('value', $singleData);
        $this->assertArrayHasKey('account', $singleData);
        $this->assertArrayHasKey('status', $singleData);
        $this->assertArrayHasKey('remark', $singleData);
    }

    protected function tearDown()
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}
