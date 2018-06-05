<?php

namespace Leven\Http\Controllers\Apis\V1;

use Leven\Helpers\CoinHelpers;
use Leven\Models\NewWallet;
use Leven\Models\User;
use Leven\Services\UserAssetsService;
use Leven\Services\UserWalletService;
use Illuminate\Http\Request;


class AssetsController extends Controller
{


    use CoinHelpers;
    public function show (Request $request){
        $user = $request->user();

        $coins=CoinHelpers::get();

        $wallets=NewWallet::where('user_id',$user->id)->get()->toArray();
        $assets=[];
        foreach ($wallets as $wallet) {
            $wallet['name']=$coins[$wallet['coin_id']]["name"];
            $wallet['symbol']=$coins[$wallet['coin_id']]["symbol"];
            unset($wallet["created_at"]);
            unset($wallet["updated_at"]);
            $assets[]=$wallet;
        }
        return response()->json(  $assets , 201);



    }

}
