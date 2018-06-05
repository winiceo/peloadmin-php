<?php

declare(strict_types=1);



namespace Leven\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Leven\Support\Configuration;
use Leven\Http\Requests\Admin\UpdateWebClientRequest;

class WebClientsController
{
    /**
     * Fetch web clients setting data.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function fetch(): JsonResponse
    {
        return response()->json([
            'web' => [
                'open' => (bool) config('http.web.open', false),
                'url' => (string) config('http.web.url', ''),
            ],
            'spa' => [
                'open' => (bool) config('http.spa.open', false),
                'url' => (string) config('http.spa.url', ''),
            ],
        ], 200);
    }

    /**
     * Update web clients settings.
     *
     * @param \Leven\Http\Requests\Admin\UpdateWebClientRequest $request
     * @param \Leven\Support\Configuration $config
     * @return \Illuminate\Http\Response
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function update(UpdateWebClientRequest $request, Configuration $config): Response
    {
        $config->set([
            'http.web.open' => (bool) $request->input('web.open'),
            'http.web.url' => $request->input('web.url'),
            'http.spa.open' => (bool) $request->input('spa.open'),
            'http.spa.url' => $request->input('spa.url'),
        ]);

        return response('', 204);
    }
}
