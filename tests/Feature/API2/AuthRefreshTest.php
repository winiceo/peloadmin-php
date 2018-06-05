<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthRefreshTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The user.
     *
     * @var Leven\Models\User
     */
    protected $user;

    /**
     * The test set up.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
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

    /**
     * Test user refresh token.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testUserLogout()
    {
        $token = $this->guard()->login($this->user);

        $response = $this->getJson('/api/v1/auth/refresh', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }
}
