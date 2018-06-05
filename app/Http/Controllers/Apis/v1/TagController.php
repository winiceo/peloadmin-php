<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Leven\Models\TagCategory as TagCategoryModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class TagController extends Controller
{
    /**
     * Get all tags.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Models\TagCategory $categoryModel
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(ResponseFactoryContract $response, TagCategoryModel $categoryModel)
    {
        return $response->json(
            $categoryModel->with('tags')->orderBy('weight', 'desc')->get()
        )->setStatusCode(200);
    }
}
