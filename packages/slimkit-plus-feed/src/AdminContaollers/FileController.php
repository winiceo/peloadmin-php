<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\AdminControllers;

use Illuminate\Http\Request;
use Leven\Http\Controllers\Controller;
use Leven\Cdn\UrlManager as CdnUrlManager;
use Leven\Models\FileWith as FileWithModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;

class FileController extends Controller
{
    /**
     * Get file.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Cdn\UrlManager $manager
     * @param \Leven\Models\FileWith $fileWith
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request, ResponseContract $response, CdnUrlManager $cdn, FileWithModel $file)
    {
        $file->load(['file']);
        $extra = array_filter([
            'width' => $request->query('w'),
            'height' => $request->query('h'),
            'quality' => $request->query('q'),
        ]);

        return $response->redirectTo(
            $cdn->make($file->file, $extra),
            302
        );
    }
}
