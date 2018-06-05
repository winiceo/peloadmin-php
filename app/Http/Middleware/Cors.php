<?php

declare(strict_types=1);



namespace Leven\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (! $response instanceof Response) {
            return $response;
        }

        $response->headers->set('Access-Control-Allow-Credentials', $this->getCredentials());
        $response->headers->set('Access-Control-Allow-Methods', $this->getMethods($request));
        $response->headers->set('Access-Control-Allow-Headers', $this->getHeaders($request));
        $response->headers->set('Access-Control-Allow-Origin', $this->getOrigin($request));
        $response->headers->set('Access-Control-Expose-Headers', implode(', ', (array) config('http.cors.expose-headers')));

        if ($maxAge = config('http.cors.max-age', 0)) {
            $response->headers->set('Access-Control-Max-Age', $maxAge);
        }

        return $response;
    }

    /**
     * Get Access-Control-Allow-Headers value.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getHeaders($request): string
    {
        $headers = (array) config('http.cors.allow-headers', []);
        if (in_array('*', $headers) && $requestHeaders = $request->headers->get('Access-Control-Request-Headers')) {
            return $requestHeaders;
        }

        return implode(', ', $headers);
    }

    /**
     * Get access-Control-Allow-Methods value.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getMethods($request): string
    {
        $methods = (array) config('http.cors.methods', []);
        if (in_array('*', $methods) && $requestMethod = $request->headers->get('Access-Control-Request-Method')) {
            return $requestMethod;
        }

        return implode(', ', $methods);
    }

    /**
     * Get Access-Control-Allow-Credentials value.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getCredentials(): string
    {
        $credentials = config('http.cors.credentials');
        if ($credentials) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Get Access-Control-Allow-Origin value.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function getOrigin($request): string
    {
        $origin = (array) config('http.cors.origin', []);

        if (in_array('*', $origin)) {
            return '*';
        }

        $requestOrigin = $request->headers->get('origin');
        if (in_array($requestOrigin, $origin)) {
            return $requestOrigin;
        }

        return (string) '';
    }
}
