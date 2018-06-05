<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Middleware;

use Closure;
use Illuminate\Http\Request;
use Leven\Traits\CreateJsonResponseData;

class VerifyCommentContent
{
    use CreateJsonResponseData;

    /**
     * check if comment_content exits.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->input('comment_content')) {
            return response()->json([
                'status' => false,
                'code' => 6007,
                'message' => '评论内容不能为空',
            ])->setStatusCode(400);
        }

        return $next($request);
    }
}
