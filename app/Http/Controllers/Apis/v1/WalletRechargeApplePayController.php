<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Http\Requests\API2\StoreWalletRecharge;
use Illuminate\Contracts\Routing\ResponseFactory as ContractResponse;

class WalletRechargeApplePayController extends WalletRechargeController
{
    /**
     * Create a Apple Pay recharge charge.
     *
     * @param \Leven\Http\Requests\API2\StoreWalletRecharge; $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function create(StoreWalletRecharge $request, ContractResponse $response)
    {
        $model = $this->createChargeModel($request, 'applepay_upacp');
        $charge = $this->createCharge($model);

        $model->charge_id = $charge['id'];
        $model->transaction_no = array_get($charge, 'credential.applepay_upacp.tn');
        $model->saveOrFail();

        return $response
            ->json(['id' => $model->id, 'charge' => $charge])
            ->setStatusCode(201);
    }
}
