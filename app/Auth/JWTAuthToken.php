<?php

declare(strict_types=1);



namespace Leven\Auth;

use Tymon\JWTAuth\JWT;
use Leven\Models\User as UserModel;

class JWTAuthToken
{
    /**
     * The \Tymon\JWTAuth\JWT instance.
     *
     * @var \Tymon\JWTAuth\JWT
     */
    protected $jwt;

    /**
     * Create the JWTAuthToken instance.
     *
     * @param \Tymon\JWTAuth\JWT $jwt
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Create user token.
     *
     * @param UserModel $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function create(UserModel $user)
    {
        return $this->jwt->fromUser($user);
    }

    /**
     * Refresh token.
     *
     * @param string $token
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function refresh(string $token)
    {
        $this->jwt->setToken($token);

        return $this->jwt->refresh();
    }
}
