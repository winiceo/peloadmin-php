<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Packages\Wallet\Order;
use Illuminate\Database\Eloquent\Builder;
use Leven\Packages\Wallet\TypeManager;
use Leven\Http\Requests\API2\NewStoreUserWallerCashPost;

class NewWalletCashController extends Controller
{
    /**
     * 获取提现列表.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $after = $request->query('after');
        $limit = $request->query('limit', 15);

        $query = $user->walletCashes();
        $query->where(function (Builder $query) use ($after) {
            if ($after) {
                $query->where('id', '<', $after);
            }
        });
        $query->limit($limit);
        $query->orderBy('id', 'desc');

        return response()
            ->json($query->get(['id', 'amount', 'address', 'remark', 'created_at']))
            ->setStatusCode(200);
    }

    /**
     * 提交提现申请.
     *
     * @param \Leven\Http\Requests\API2\NewStoreUserWallerCashPost $request
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function store(NewStoreUserWallerCashPost $request, TypeManager $manager)
    {

        $type = "mainnet";
        $amount = (int)$request->input('amount');
        $address = $request->input('address');
        $coin_id = (int)$request->input('coin_id');
        $user = $request->user();



        if ($manager->driver(Order::TARGET_TYPE_WITHDRAW)->widthdraw($user, $amount, $coin_id,$type, $address) === true) {
            return response()
                ->json(['message' => '提交申请成功'])
                ->setStatusCode(201);
        }

        return response()->json(['message' => '操作失败'], 500);
    }
}
