<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Leven\Support\ManageRepository;
use Leven\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Admin home.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $data = [
            'csrf_token' => csrf_token(),
            'base_url'   => url('admin'),
            'api'        => url('api/v1'),
            'logged'     => (bool) $user,
            'user'       => $user,
            'token' => JWTAuth::fromUser($user),
        ];

        return view('admin', $data);
    }

    /**
     * 后台导航菜单.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function showManages(ManageRepository $repository)
    {
        return response()
            ->json($repository->getManages())
            ->setStatusCode(200);
    }
}
