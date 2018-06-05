<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Models\WalletCash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Leven\Http\Requests\API2\StoreUserWallerCashPost;

class WalletCashController extends Controller
{
    /**
     * 获取提现列表.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
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
            ->json($query->get(['id', 'amount', 'balance', 'address', 'txid', 'remark', 'created_at']))
            ->setStatusCode(200);
    }

    /**
     * 提交提现申请.
     *
     * @param \Leven\Http\Requests\API2\StoreUserWallerCashPost $request
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(StoreUserWallerCashPost $request)
    {
        $amount = $request->input('amount');
        $address = $request->input('address');
        $coin_id = $request->input('coin_id');
        $user = $request->user();


        // Create Cash.
        $cash = new WalletCash();
        $cash->amount = $amount;
        $cash->address = $address;
        $cash->coin_id = $coin_id;
        $cash->status = 0;


        DB::transaction(function () use ($user, $amount, $cash) {
            $user->wallet()->decrement('balance', $amount);
            $user->walletCashes()->save($cash);
        });

        return response()
            ->json(['message' => '提交申请成功'])
            ->setStatusCode(201);
    }
}
