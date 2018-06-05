<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Helpers\CoinHelpers;
use Leven\Models\GoldType;
use Leven\Models\CommonConfig;
use Leven\Models\CurrencyType;
use Leven\Models\AdvertisingSpace;
use Leven\Support\BootstrapAPIsEventer;
use Illuminate\Contracts\Routing\ResponseFactory;

class CoinController extends Controller
{

    use CoinHelpers;
    public function price (ResponseFactory $response){
        $coins=CoinHelpers::get();
        $assets=[];
        foreach ($coins as $coin) {
            $coin["price"]=0.2;
            $assets[]=$coin;
        }
        return $response->json($assets, 200);

    }
}
