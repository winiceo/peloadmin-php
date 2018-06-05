<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Leven\Models\Advertising;
use Leven\Models\AdvertisingSpace;

class AdvertisingController extends Controller
{
    /**
     * Get installed ad slot information.
     *
     * @author bs<414606094@qq.com>
     * @param  AdvertisingSpace $space
     * @return mix
     */
    public function index(AdvertisingSpace $space): JsonResponse
    {
        $space = $space->select('id', 'channel', 'space', 'alias', 'allow_type', 'format', 'created_at', 'updated_at')->get();

        return response()->json($space, 200);
    }

    /**
     * 查询某一广告位的广告列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request          $request
     * @param  AdvertisingSpace $space
     * @return mix
     */
    public function advertising(AdvertisingSpace $space)
    {
        $space->load(['advertising' => function ($query) {
            return $query->orderBy('sort', 'asc');
        }]);

        return response()->json($space->advertising, 200);
    }

    /**
     * 批量获取广告列表.
     *
     * @author bs<414606094@qq.com>
     * @param  Request $request
     * @return json
     */
    public function batch(Request $request, Advertising $advertisingModel)
    {
        $space = explode(',', $request->query('space'));
        $advertising = $advertisingModel->whereIn('space_id', $space)->orderBy('sort', 'asc')->get();

        return response()->json($advertising, 200);
    }
}
