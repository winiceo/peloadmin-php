<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use Illuminate\Support\Facades\Artisan;
use Leven\Http\Controllers\Controller;

class AuxiliaryController extends Controller
{
    /**
     * 清除缓存.
     *
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function cleanCache()
    {
        Artisan::call('cache:clear');

        return response()->json([], 200);
    }
}
