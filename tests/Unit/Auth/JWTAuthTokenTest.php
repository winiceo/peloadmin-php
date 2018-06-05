<?php



namespace Leven\Tests\Unit\Auth;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JWTAuthTokenTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test create method.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testCreate()
    {
        $jwtAuthToken = $this->app->make(\Leven\Auth\JWTAuthToken::class);
        $user = factory(UserModel::class)->create();
        $token = $jwtAuthToken->create($user);

        $this->assertTrue((bool) $token);
    }

    /**
     * Test refresh method.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testRefresh()
    {
        $jwtAuthToken = $this->app->make(\Leven\Auth\JWTAuthToken::class);
        $user = factory(UserModel::class)->create();
        $token = $jwtAuthToken->create($user);
        $newToken = $jwtAuthToken->refresh($token);

        $this->assertTrue((bool) $newToken);
        $this->assertNotSame($token, $newToken);
    }
}
