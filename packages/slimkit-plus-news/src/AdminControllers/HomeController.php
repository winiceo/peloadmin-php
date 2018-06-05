<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\AdminControllers;

use Illuminate\Http\Request;
use Leven\Auth\JWTAuthToken;
use Leven\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class HomeController extends Controller
{
    use AuthenticatesUsers {
        login as traitLogin;
    }

    public function show(Request $request, JWTAuthToken $jwt)
    {
        if (! $request->user()) {
            return redirect(route('admin'), 302);
        }

        config('jwt.single_auth', false);

        return view('plus-news::admin', [
            'token' => $jwt->create($request->user()),
            'base_url' => route('news:admin'),
            'csrf_token' => csrf_token(),
            'api' => url('api/v2'),
            'files' => url('/api/v2/files'),
        ]);
    }

    protected function menus()
    {
        $components = config('component');
        $menus = [];

        foreach ($components as $component => $info) {
            $info = (array) $info;
            $installer = array_get($info, 'installer');
            $installed = array_get($info, 'installed', false);

            if (! $installed || ! $installer) {
                continue;
            }

            $componentInfo = app($installer)->getComponentInfo();

            if (! $componentInfo) {
                continue;
            }

            $menus[$component] = [
                'name'  => $componentInfo->getName(),
                'icon'  => $componentInfo->getIcon(),
                'logo'  => $componentInfo->getLogo(),
                'admin' => $componentInfo->getAdminEntry(),
            ];
        }

        return $menus;
    }
}
