<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Models\User as UserModel;
use Leven\Models\Taggable as TaggableModel;
use Leven\Models\UserExtra as UserExtraModel;
use Leven\Models\UserRecommended as UserRecommendedModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseContract;

/**
 * 找人.
 */
class FindUserController extends Controller
{
    /**
     * 热门用户, 根据粉丝数量倒序排列. 无需登录.
     */
    public function populars(Request $request, UserExtraModel $userExtra, ResponseContract $response)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $user_id = $request->user('api')->id ?? 0;

        $users = $userExtra
            ->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
            ->whereExists(function ($query) {
                return $query->from('users')->whereRaw('users.id = user_extras.user_id')->where('deleted_at', null);
            })
            ->limit($limit)
            ->select('user_id')
            ->with([
                'user',
            ])
            ->orderBy('followers_count', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $response->json(
            $users->map(function ($user) use ($user_id) {
                $user->user->following = $user->user->hasFollwing($user_id);
                $user->user->follower = $user->user->hasFollower($user_id);

                return $user->user;
            })
        )
        ->setStatusCode(200);
    }

    /**
     * 最新用户,按注册时间倒序. 无需登录.
     */
    public function latests(Request $request, UserModel $user, ResponseContract $response)
    {
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', null);
        $user_id = $request->user('api')->id ?? 0;

        $users = $user->when($offset, function ($query) use ($offset) {
            return $query->offset($offset);
        })
            ->latest()
            ->limit($limit)
            ->get();

        return $response->json(
            $users->map(function ($user) use ($user_id) {
                $user->following = $user->hasFollwing($user_id);
                $user->follower = $user->hasFollower($user_id);

                return $user;
            })
        )
        ->setStatusCode(200);
    }

    /**
     * 推荐用户. 无需登录.
     */
    public function recommends(Request $request, UserRecommendedModel $userRecommended, ResponseContract $response)
    {
        $user_id = $request->user('api')->id ?? 0;
        $limit = $request->input('limit', 200);
        $offset = $request->input('offset', 0);

        $users = $userRecommended->when($offset, function ($query) use ($offset) {
            return $query->offset($offset);
        })
        ->whereExists(function ($query) {
            return $query->from('users')->whereRaw('users.id = users_recommended.user_id')->where('deleted_at', null);
        })
        ->with(['user'])
        ->limit($limit)
        ->orderBy('id', 'desc')
        ->get();

        return $response->json(
            $users->map(function ($user) use ($user_id) {
                $user->user->following = $user->user->hasFollwing($user_id);
                $user->user->follower = $user->user->hasFollower($user_id);

                return $user->user;
            })
        )
        ->setStatusCode(200);
    }

    /**
     * search users by name. 无需登录.
     */
    public function search(Request $request, UserModel $user, ResponseContract $response, UserRecommendedModel $userRecommended)
    {
        $user_id = $request->user('api')->id ?? 0;
        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        $keyword = $request->input('keyword', null);

        // 没有输入关键字，返回后台推荐用户
        if (! $keyword) {
            $users = $userRecommended->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
                ->with(['user'])
                ->limit($limit)
                ->orderBy('id', 'desc')
                ->get();

            return $response->json(
                $users->map(function ($user) use ($user_id) {
                    $user->user->following = $user->user->hasFollwing($user_id);
                    $user->user->follower = $user->user->hasFollower($user_id);
                    $user->user->load('tags');

                    return $user->user;
                })
            )
            ->setStatusCode(200);
        }

        $users = $user->where('name', 'like', "%{$keyword}%")
            ->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
            ->with('tags')
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        return $response->json(
            $users->map(function ($user) use ($user_id) {
                $user->following = $user->hasFollwing($user_id);
                $user->follower = $user->hasFollower($user_id);

                return $user;
            })
        )
        ->setStatusCode(200);
    }

    /**
     * 通过标签推荐用户. 未登录则返回空数组.
     */
    public function findByTags(Request $request, TaggableModel $taggable, ResponseContract $response)
    {
        $currentUser = $request->user('api');

        if (! $currentUser) {
            return response()->json([])->setStatusCode(200);
        }

        $limit = $request->input('limit', 15);
        $offset = $request->input('offset', 0);
        // $recommends = $users = [];

        $tags = $currentUser->tags()->select('tag_id')->get();
        $tags = array_pluck($tags, 'tag_id');
        // 根据用户标签获取用户
        $users = $taggable->whereIn('tag_id', $tags)
            ->where('taggable_id', '<>', $currentUser->id)
            ->where('taggable_type', 'users')
            ->whereExists(function ($query) {
                return $query->from('users')->whereRaw('users.id = taggables.taggable_id')->where('deleted_at', null);
            })
            ->when($offset, function ($query) use ($offset) {
                return $query->offset($offset);
            })
            ->with('user')
            ->limit($limit)
            ->select('taggable_id')
            ->groupBy('taggable_id')
            ->get();

        return $response->json(
            $users->map(function ($user) use ($currentUser) {
                $user->user->following = $user->user->hasFollwing($currentUser->id);
                $user->user->follower = $user->user->hasFollower($currentUser->id);

                return $user->user;
            })
        )
        ->setStatusCode(200);
    }

    /**
     * 通过手机号查找,无需登录.
     */
    public function findByPhone(Request $request, UserModel $user, ResponseContract $response)
    {
        $user_id = $request->user('api')->id ?? 0;
        $phones = $request->input('phones', '');

        if (! $phones) {
            abort(422, '请传递手机号码');
        }

        $users = $user
            ->select('*')
            ->whereIn('phone', $phones)
            ->limit(100)
            ->get();

        return $response->json(
            $users->map(function ($user) use ($user_id) {
                $user->following = $user->hasFollwing($user_id);
                $user->follower = $user->hasFollower($user_id);
                $user->mobi = $user->phone;

                return $user;
            })
        )
        ->setStatusCode(200);
    }
}