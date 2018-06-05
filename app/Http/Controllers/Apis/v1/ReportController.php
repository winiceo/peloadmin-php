<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Models\User as UserModel;
use Leven\Models\Report as ReportModel;
use Leven\Models\Comment as CommentModel;

class ReportController extends Controller
{
    /**
     * 举报一个用户.
     *
     * @param Request $request
     * @param UserModel $user
     * @param ReportModel $reportModel
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function user(Request $request, UserModel $user, ReportModel $reportModel)
    {
        $auth_user = $request->user();

        $reportModel->user_id = $auth_user->id;
        $reportModel->target_user = $user->id;
        $reportModel->status = 0;
        $reportModel->reason = $request->input('reason');
        $reportModel->subject = sprintf('用户：%s', $user->name);

        $user->reports()->save($reportModel);

        return response()->json(['message' => '操作成功'], 201);
    }

    /**
     * 举报一条评论.
     *
     * @param Request $request
     * @param CommentModel $comment
     * @param ReportModel $reportModel
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function comment(Request $request, CommentModel $comment, ReportModel $reportModel)
    {
        $auth_user = $request->user();

        $reportModel->user_id = $auth_user->id;
        $reportModel->target_user = $comment->user_id;
        $reportModel->status = 0;
        $reportModel->reason = $request->input('reason');
        $reportModel->subject = sprintf('评论：%s', mb_substr($comment->body, 0, 50));

        $comment->reports()->save($reportModel);

        return response()->json(['message' => '操作成功'], 201);
    }
}
