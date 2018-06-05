<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Leven\Models\Tag as TagModel;
use Leven\Models\User as UserModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class TagUserController extends Controller
{
    /**
     * Get all tags of the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function index(Request $request, ResponseFactoryContract $response)
    {
        return $this->userTgas($response, $request->user()->id);
    }

    /**
     * Attach a tag for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Models\Tag $tag
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(Request $request, ResponseFactoryContract $response, TagModel $tag)
    {
        $user = $request->user();
        if ($user->tags()->newPivotStatementForId($tag->id)->first()) {
            return $response->json([
                'message' => [
                    trans('tag.user.attached', [
                        'tag' => $tag->name,
                    ]),
                ],
            ], 422);
        }

        $user->tags()->attach($tag);

        return $response->make('', 204);
    }

    /**
     * Detach a tag for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Models\Tag $tag
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function destroy(Request $request, ResponseFactoryContract $response, TagModel $tag)
    {
        $user = $request->user();

        if (! $user->tags()->newPivotStatementForId($tag->id)->first()) {
            return $response->json([
                'message' => [
                    trans('tag.user.destroyed', [
                        'tag' => $tag->name,
                    ]),
                ],
            ], 422);
        }

        $user->tags()->detach($tag);

        return $response->make('', 204);
    }

    /**
     * Get the user's tags.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response [description]
     * @param \Leven\Models\User $user [description]
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function userTgas(ResponseFactoryContract $response, int $user)
    {
        $user = UserModel::withTrashed()->find($user);

        return $response->json($user->tags, 200);
    }
}
