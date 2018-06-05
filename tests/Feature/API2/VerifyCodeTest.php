<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VerifyCodeTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create(['phone' => '13730441111', 'email' => 'aaa@bbb.com']);
    }

    /**
     * 测试通过手机号获取验证码.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetVerifyCodeByPhone()
    {
        $token = $this->guard()->login($this->user);

        $responseByPhone = $this->json('POST', 'api/v2/verifycodes?token='.$token, [
            'phone' => $this->user->phone,
        ]);

        $this->assertLoginResponse($responseByPhone);
    }

    /**
     * 测试通过邮箱获取验证码.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetVerifyCodeByEmail()
    {
        $token = $this->guard()->login($this->user);

        $responseByEmail = $this->json('POST', 'api/v2/verifycodes?token='.$token, [
            'email' => $this->user->email,
        ]);

        $this->assertLoginResponse($responseByEmail);
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

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard(): Guard
    {
        return Auth::guard('api');
    }

    protected function tearDown()
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}
