<?php

declare(strict_types=1);



namespace Leven\Http\Middleware;

use Closure;
use Leven\Models\Sensitive as SensitiveModel;

class DisposeSensitive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$inputs)
    {
        $map = [];
        foreach ($inputs as $input) {
            if ($request->has($input)) {
                $map[$input] = $request->$input;
            }
        }

        $warnings = SensitiveModel::where('type', 'warning')->get();
        foreach ($map as $key => $value) {
            foreach ($warnings as $sensitive) {
                if (strpos((string) $value, $sensitive->word) === false) {
                    continue;
                }

                return response()->json([
                    'message' => '内容包含敏感词',
                    'errors' => [$key => [sprintf('内容包含「%s」为敏感词！', $sensitive->word)]],
                ], 422);
            }
        }

        $replaces = SensitiveModel::where('type', 'replace')
            ->get()
            ->keyBy('word')
            ->map(function ($sensitive) {
                return $sensitive->replace;
            })
            ->toArray();
        $map = array_map(function ($value) use ($replaces) {
            return strtr((string) $value, $replaces);
        }, $map);
        $souceInput = $request->except($inputs);
        $request->replace($map);
        $request->merge($souceInput);

        return $next($request);
    }
}
