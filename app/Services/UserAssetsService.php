<?php
/**
 * Created by PhpStorm.
 * User: genv
 * Date: 2017/11/30
 * Time: ä¸???8:21
 */

namespace Leven\Service;


use Leven\Helpers\CoinHelpers;
use Leven\Models\Assets;
use Leven\Models\Order;
use Leven\Models\User;
use Leven\Models\UserBalance;
use Leven\Models\UserWallet;
use Leven\Models\WalletAddress;
use Leven\Models\UserWithdraw;
use Illuminate\Contracts\Pagination\Paginator;


class UserAssetsService
{
    use CoinHelpers;


    public static function checkWallet(User $user)
    {

        $coins = CoinHelpers::get();

        foreach ($coins as $coin) {
            $balance = UserBalance::where('coin_type', $coin['id'])
                ->where('user_id', $user->id)
                ->first();

            Log::info('balance', [$balance]);
            if (!$balance) {
                $data["user_id"] = $user->id;
                $data["coin_type"] = $coin['id'];
                $data["coin_name"] = $coin['name'];
                $data["block_balance"] = 0;
                $data["pending_balance"] = 0;
                $data["total_balance"] = 0;
                UserBalance::create($data);
            }

        }

    }

    public static function index(User $user)
    {

        $coins = CoinHelpers::getIds();

        $assets = Assets::where('user_id', $user->id)
            ->get();
        $data = [];
        foreach ($assets as $asset) {
            $coin = $coins[$asset['coin_id']];
            unset($coin["id"]);
            $data[] = array_merge($asset->toArray(), $coin);
        }

        return $data;
    }


    public static function storeWithdraw($data)
    {


        $data["type"] = Container::get("config")["ORDER_TYPE"]['WITHDRAW'];
        $ret = Order::create($data);

        self::userBalancePending($ret);
        return $ret;

    }

    public static function userBalancePending($order)
    {
        $uid = $order->user_id;
        $coin_id = $order->coin_id;
        $block_balance = $order->amount;

        $asset = Assets::where('user_id', $uid)
            ->where('coin_id', $coin_id)
            ->decrement('balance', $block_balance);
        if ($asset) {
            $data = Assets::where('user_id', $uid)
                ->where('coin_id', $coin_id)->first()->toArray();
            $order->balance = $data["balance"];
            $order->save();
        }


        Log::info('balance', ['message' => 'pending increment balance: ' . $block_balance]);

    }


    public static function getHistory(Request $request, User $user, int $perPage = 20)
    {

        $order_by = $request->getParam('order_by', "id");
        $desc = $request->getParam('desc', 1);
        $page = (int)$request->getParam('page', 1);
        $type = (int)$request->getParam('type', 0);
        if($page<1){
            $page=1;
        }

        $res= Order::where(function ($query) use ($request, $user,$type) {

            $query->where('user_id', $user->id);
            $status = $request->getParam('status', -1);
            if ($status !== -1) {
                $query->where('status', $status);
            }
            if ($type > 0) {
                $query->where('type', $type);
            }

        })->orderBy($order_by, $desc == 1 ? "desc" : "asc")
            ->paginate($perPage, ['*'],"page",$page);

        $data=[];
        $data["data"]=$res->items();
        $data["page"]=[
            "current_page"=>$res->currentPage(),
            "total_page"=>$res->lastPage(),
            "total"=>$res->total(),
        ];
        return $data;

    }


}