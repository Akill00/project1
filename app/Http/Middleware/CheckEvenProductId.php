<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

class CheckEvenProductId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
        public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $content = json_decode($response->getContent(), true);

        if (isset($content['data']['data']) && is_array($content['data']['data'])) {
            foreach ($content['data']['data'] as $product) {
                if (isset($product['id']) && $product['id'] % 2 !== 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Access denied. Product ID must be even.',
                    ], 403);
                }
            }
        }

        return $response;
    }


}
