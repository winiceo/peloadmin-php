<?php
/**
 * Created by PhpStorm.
 * User: genv
 * Date: 2017/11/30
 * Time: ф╕???8:21
 */

namespace Leven\Service;


use Leven\Helpers\CoinHelpers;
use Leven\Models\User;
use Leven\Models\UserBalance;
use Leven\Models\UserWallet;
use Leven\Models\WalletAddress;
use Leven\Models\UserWithdraw;
use Illuminate\Contracts\Pagination\Paginator;




class UserWalletService
{
    use CoinHelpers;


    public static function checkWallet(User $user)
    {

        $coins = CoinHelpers::get();

        foreach ($coins as $coin) {
            $balance = UserBalance::where('coin_type', $coin['id'])
                ->where('user_id', $user->id)
                ->first();

            Log::info('balance',[$balance]);
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

    public static function address(User $user)
    {

        $coins = CoinHelpers::get();
        $balances = UserWallet::where('user_id', $user->id)
            ->get();
        return $balances;

    }

    public static function storeAddress($data)
    {
        $ret = UserWallet::create($data);
        return $ret;

    }

    //?дц??????хн???
    public static function isExistAddress($data)
    {
        return UserWallet::where('address', $data['address'])
            ->where('user_id', $data['user_id'])
            ->first();
    }

    public static function getWalletAddress($user_id, $coin_type)
    {

        $address = WalletAddress::where('coin_type', $coin_type)
            ->where('user_id', $user_id)
            ->first();


        if (!$address) {
            $address = WalletAddress::where('coin_type', $coin_type)
                ->where('user_id', 0)->first();
            $address->user_id = $user_id;
            $address->save();
        }
        return $address;

    }

    public static function storeWithdraw($data)
    {
        $ret = UserWithdraw::create($data);

        self::userBalancePending($ret);
        return $ret;

    }

    public static  function userBalancePending($order ){
        $uid=$order->user_id;
        $coin_type=$order->coin_type;
        $block_balance=$order->amount;

        UserBalance::where('user_id',$uid)
            ->where('coin_type',$coin_type)
            ->increment('pending_balance', $block_balance);


        Log::info('balance',['message'=>'pending increment balance: '.$block_balance]);

    }


    public static function getHistory(Request $request, User $user, int $perPage = 20): Paginator
    {

        $order_by = $request->getParam('order_by', "id");
        $desc = $request->getParam('desc', 1);

        return UserWithdraw::where(function ($query) use ($request, $user) {

            $query->where('user_id', $user->id);
            $status = $request->getParam('status', -1);
            if ($status !== -1) {
                $query->where('status', $status);
            }

        })->orderBy($order_by, $desc == 1 ? "desc" : "asc")
            ->paginate($perPage);
    }


}