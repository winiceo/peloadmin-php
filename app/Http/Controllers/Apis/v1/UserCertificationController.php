<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Apis\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Leven\Models\FileWith as FileWithModel;
use Leven\Models\Certification as CertificationModel;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
use Leven\Http\Requests\API2\UserCertification as UserCertificationRequest;

class UserCertificationController extends Controller
{
    /**
     * Get a user certification.
     *
     * @param \Illuminate\Http\Request $request [description]
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response [description]
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function show(Request $request, ResponseFactoryContract $response)
    {
        $user = $request->user();

        return $response->json($user->certification, 200);
    }

    /**
     * Send certification.
     *
     * @param \Leven\Http\Requests\API2\UserCertification $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Models\Certification $certification
     * @param \Leven\Models\FileWith $fileWithModel
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function store(
        UserCertificationRequest $request,
        ResponseFactoryContract $response,
        CertificationModel $certification,
        FileWithModel $fileWithModel
    ) {
        $user = $request->user();
        $type = $request->input('type');
        $data = $request->only(['name', 'phone', 'number', 'desc']);
        $files = $this->findNotWithFileModels($request, $fileWithModel);

        $data['files'] = $files->pluck('id');
        if ($type === 'org') {
            $data = array_merge($data, $request->only(['org_name', 'org_address']));
        }

        $certification->certification_name = $type;
        $certification->data = $data;
        $certification->status = 0;

        return $certification->getConnection()->transaction(function () use ($user, $files, $certification, $response) {
            $files->each(function ($file) use ($user) {
                $file->channel = 'certification:file';
                $file->raw = $user->id;
                $file->save();
            });
            $user->certification()->save($certification);

            return $response->json(['message' => ['提交成功，等待审核']])->setStatusCode(201);
        });
    }

    /**
     * Update certification.
     *
     * @param \Leven\Http\Requests\API2\UserCertification $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     * @param \Leven\Models\FileWith $fileWithModel
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function update(
        UserCertificationRequest $request,
        ResponseFactoryContract $response,
        FileWithModel $fileWithModel
    ) {
        $user = $request->user();
        $type = $request->input('type');
        $certification = $user->certification;

        if ($certification->status === 1) {
            return $response->json(['message' => ['已审核通过，无法修改']], 422);
        }

        $updateData = $request->only(['name', 'phone', 'number', 'desc']);
        if ($type === 'org') {
            $updateData = array_merge($updateData, $request->only(['org_name', 'org_address']));
        }

        $files = $this->findNotWithFileModels($request, $fileWithModel);
        $fileIds = array_values(
            array_filter((array) $request->input('files', []))
        );

        if (! empty($fileIds)) {
            $updateData['files'] = $fileIds;
        }

        $certification->certification_name = $type ?: $certification->certification_name;
        $certification->data = array_merge($certification->data, array_filter($updateData));
        $certification->status = 0;

        return $user->getConnection()->transaction(function () use ($user, $files, $certification, $response) {
            $files->each(function ($file) use ($user) {
                $file->channel = 'certification:file';
                $file->raw = $user->id;
                $file->save();
            });
            $certification->save();

            return $response->json(['message' => ['修改成功，等待审核']], 201);
        });
    }

    /**
     * File not with file models.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Leven\Models\FileWith $fileWithModel
     * @return \Illuminate\Support\Collection
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function findNotWithFileModels(Request $request, FileWithModel $fileWithModel): Collection
    {
        $files = new Collection(
            array_filter((array) $request->input('files', []))
        );

        if ($files->isEmpty()) {
            return $files;
        }

        return $fileWithModel->where('channel', null)
            ->where('raw', null)
            ->whereIn('id', $files)
            ->get();
    }
}
