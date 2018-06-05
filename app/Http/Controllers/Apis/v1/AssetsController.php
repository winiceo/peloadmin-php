<?php

namespace Leven\Http\Controllers\Apis\V1;

use Leven\Models\User;
use Leven\Services\UserAssetsService;
use Leven\Services\UserWalletService;


class AssetsController extends Controller
{


    //资产列表
    public function index(Request $request, Response $response)
    {
        $user = $this->auth->getUser();

        $assets = UserAssetsService::index($this->auth->getUser());
        return $this->json($response, $assets);
    }

    /**
     * 提现
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function withdraw(Request $request, Response $response)
    {


        if ($request->isPost()) {
            $this->validator->request($request, [
                'coin_id' => v::numeric()->length(1, 1),
                'address' => v::stringType()->notEmpty(),
                'amount' => v::numeric()->length(1, 20)->notEmpty()

            ]);
            $data = $request->getParams();

            $data["user_id"] = $this->auth->getUser()->getUserId();

            $data['order_code'] = time();

            if ($this->validator->isValid()) {
                $address = UserAssetsService::storeWithdraw($data);
                return $this->json($response, $address);
            } else {
                return $this->error("input error");
            }

        }
        return $this->error($response);

    }


    public function history(Request $request, Response $response)
    {

        $withdraws = UserAssetsService::getHistory($request, $this->auth->getUser(), 20);
        return $this->json($response, $withdraws);
    }

}
