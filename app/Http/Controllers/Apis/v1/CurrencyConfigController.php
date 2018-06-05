<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Models\CommonConfig;
use Leven\Repository\CurrencyConfig;

class CurrencyConfigController extends Controller
{
    /**
     * 获取积分相关配置.
     *
     * @param CurrencyConfig $config
     * @return array
     * @author BS <414606094@qq.com>
     */
    public function show(CurrencyConfig $config)
    {
        $configs = array_merge($config->get(), [
            'rule' => config('currency.rule', ''),
            'recharge-rule' => config('currency.recharge.rule', ''),
            'cash' => ($cash = CommonConfig::where('name', 'cash')->where('namespace', 'wallet')->first()) ? json_decode($cash->value) : [],
            'cash-rule' => config('currency.cash.rule', ''),
            'apple-IAP-rule' => config('currency.recharge.IAP.rule', ''),
            'recharge-type' => ($recharge_type = CommonConfig::where('name', '    wallet:recharge-type')->where('namespace', 'common')->first()) ? json_decode($recharge_type->value) : [],
        ]);

        return response()->json($configs, 200);
    }
}
