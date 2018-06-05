<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Leven\Http\Requests\API2\StoreCoin;
use Leven\Models\Coins as CoinModel;
use Leven\Http\Controllers\Controller;
use Leven\Http\Requests\API2\UpdateTag;

/**
 * 标签管理控制器.
 */
class CoinController extends Controller
{
    /**
     * 标签列表.
     */
    public function lists(Request $request, CoinModel $coin_model)
    {


         $keyword = $request->input('keyword');

        $coins = $coin_model

            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', sprintf('%%%s%%', $keyword));
            })
            ->orderBy('weight', 'desc')
            ->orderBy('id', 'asc')->get();




        return response()->json($coins)->setStatusCode(200);
    }

     
    // 获取单个coin信息
    public function coin(CoinModel $coin)
    {
        return response()->json($coin)->setStatusCode(200);
    }

    /**
     * 删除coin.
     */
    public function delete(CoinModel $coin)
    {
        if (! $coin->coingable()->count()) {
            $coin->delete();

            return response()->json()->setStatusCode(204);
        }

        return response()->json(['message' => '有资源使用该标签，不能删除，请先清理使用该标签的资源'])->setStatusCode(422);
    }

    // 获取无分页coin分类
    public function cateForTag()
    {
        $categories = TagCategoryModel::orderBy('id', 'asc')
            ->orderBy('weight', 'desc')
            ->get();

        return response()->json($categories)->setStatusCode(200);
    }

    // 新增coin
    public function store(StoreCoin $request, CoinModel $coin)
    {
        $name = $request->input('name');
        $symbol = $request->input('symbol');
        $decimals = $request->input('decimals');
        $withdraw_enable = $request->input('withdraw_enable');
        $fee = $request->input('fee');
        $weight = $request->input('weight', 0);

        $coin->name = $name;
        $coin->symbol = $symbol;
        $coin->decimals = $decimals;
        $coin->withdraw_enable = $withdraw_enable;
        $coin->fee = $fee;
        $coin->weight = $weight;

        $coin->save();

        return response()->json(['message' => '增加成功'])->setStatusCode(201);
    }

    /**
     * 更新coin.
     */
    public function update(UpdateTag $request, CoinModel $coin)
    {
        $name = $request->input('name', '');
        $coin_category_id = $request->input('category');
        $weight = $request->input('weight');

        $name && $coin->name = $name;

        if ($coin_category_id) {
            $coin->coin_category_id = $coin_category_id;
        }

        $weight !== null && $coin->weight = $weight;

        if ($coin->save()) {
            return response()->json(['message' => '修改成功'])->setStatusCode(201);
        }

        return response()->json(['message' => '未知错误'])->setStatusCode(500);
    }

    /**
     * 删除标签分类.
     */
    public function deleteCategory(TagCategoryModel $cate)
    {
        if (! $cate->coins()->count()) {
            $cate->delete();

            return response()->json()->setStatusCode(204);
        }

        return response()->json(['message' => '该分类下还有标签存在，请先删除标签再删除分类'])->setStatusCode(422);
    }

    /**
     * 存储标签分类.
     */
    public function storeCate(Request $request, TagCategoryModel $coincate)
    {
        $name = $request->input('name', '');
        $weight = $request->input('weight', 0);

        if (! $name) {
            return response()->json(['message' => '请输入分类名称'])->setStatusCode(400);
        }

        if ($coincate->where('name', $name)->count()) {
            return response()->json(['message' => '分类已经存在'])->setStatusCode(422);
        }

        $coincate->weight = $weight;
        $coincate->name = $name;
        $coincate->save();

        return response()->json(['message' => '创建分类成功', 'id' => $coincate->id])->setStatusCode(201);
    }

    /**
     * 更新标签分类.
     */
    public function updateCate(Request $request, TagCategoryModel $cate)
    {
        $name = $request->input('name', '');
        $weight = $request->input('weight');
        if ($name && TagCategoryModel::where('name', $name)->count()) {
            return response()->json(['message' => '分类已经存在'])->setStatusCode(422);
        }

        $weight !== null && $cate->weight = $weight;
        $name && $cate->name = $name;
        $cate->save();

        return response()->json(['message' => '修改成功'])->setStatusCode(201);
    }
}
