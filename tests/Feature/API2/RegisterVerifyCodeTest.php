<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterVerifyCodeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 测试获取验证码.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetVerifyCode()
    {
        $registerByPhone = $this->json('POST', 'api/v1/verifycodes/register', [
            'phone' => '13730111234',
        ]);

        $registerByEmail = $this->json('POST', 'api/v1/verifycodes/register', [
            'email' => 'aaa@bbb.com',
        ]);

        $this->assertLoginResponse($registerByPhone);
        $this->assertLoginResponse($registerByEmail);
    }

    /**
     * Assert login response.
     *
     * @param $response
     * @return void
     */
    protected function assertLoginResponse($response)
    {
        $response
            ->assertStatus(202)
            ->assertJsonStructure(['message']);
    }
}
