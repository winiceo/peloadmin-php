<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Leven\Models\CommonConfig;
use Leven\Support\Configuration;
use Leven\Http\Controllers\Controller;

class CurrencyAppleController extends Controller
{
    /**
     * 获取苹果IAP设置.
     *
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function getConfig()
    {
        $config['IAP_only'] = config('currency.recharge.IAP.only', true);
        $config['rule'] = config('currecy.recharge.IAP.rule', '');

        return response()->json($config, 200);
    }

    /**
     * 保存IAP配置.
     *
     * @param Request $request
     * @param Repository $config
     * @author BS <414606094@qq.com>
     */
    public function setConfig(Request $request, Configuration $configuration)
    {
        $IAP_config = (bool) $request->input('IAP_only');

        $config = $configuration->getConfiguration();
        $config->set('currency.recharge.IAP.only', $IAP_config);
        $config->set('currency.recharge.IAP.rule', $request->input('rule', ''));
        $configuration->save($config);

        return response()->json(['message' => '保存成功'], 201);
    }

    /**
     * 获取苹果商品列表.
     *
     * @param CommonConfig $configModel
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function getProducts(CommonConfig $configModel)
    {
        $products = ($datas = $configModel->where('name', 'product')->where('namespace', 'apple')->first()) ? json_decode($datas->value) : [];

        return response()->json($products);
    }

    /**
     * 添加商品.
     *
     * @param Request $request
     * @author BS <414606094@qq.com>
     */
    public function addProduct(Request $request, CommonConfig $configModel)
    {
        $this->validate($request, $this->getProductRule(), $this->getProductMessage());

        $addProductInfo = $request->only('product_id', 'name', 'apple_id', 'amount');
        $datas = $configModel->where('name', 'product')->where('namespace', 'apple')->first();

        if (! $datas) {
            CommonConfig::create(['name' => 'product', 'namespace' => 'apple', 'value' => json_encode([$addProductInfo])]);

            return response()->json(['message' => '添加成功'], 201);
        }
        $products = json_decode($datas->value, true);
        if (in_array($addProductInfo['product_id'], collect($products)->pluck('product_id')->toArray())) {
            return response()->json(['message' => '产品id已存在'], 422);
        }

        $datas->value = json_encode(array_merge($products, [$addProductInfo]));
        $datas->save();

        return response()->json(['message' => '添加成功'], 201);
    }

    /**
     * 删除商品.
     *
     * @param Request $request
     * @param CommonConfig $configModel
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function delProduct(Request $request, CommonConfig $configModel)
    {
        $datas = $configModel->where('name', 'product')->where('namespace', 'apple')->first();

        if (! $datas) {
            return response()->json(['message' => '商品不存在'], 404);
        }

        $products = collect(json_decode($datas->value));
        $product_id = $request->input('product_id');
        if (! in_array($product_id, $products->pluck('product_id')->toArray())) {
            return response()->json(['message' => '商品不存在'], 404);
        }

        $products = $products->where('product_id', '!=', $product_id)->all();
        $datas->value = json_encode($products);
        $datas->save();

        return response()->json(['message' => '删除成功'], 204);
    }

    protected function getProductRule()
    {
        return [
            'product_id' => 'required',
            'name' => 'required',
            'apple_id' => 'required',
            'amount' => 'required',
        ];
    }

    protected function getProductMessage()
    {
        return [
            'product_id.required' => '请输入产品id',
            'name.required' => '请输入产品名',
            'apple_id.required' => '请输入apple_id',
            'amount.required' => '请输入产品定价',
        ];
    }
}
