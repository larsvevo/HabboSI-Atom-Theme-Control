<?php

namespace Atom\Theme\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods()) && in_array('api', $route->middleware()))
            ->filter(fn ($route) => str_contains($route->uri(), 'api/public'))
            ->map(fn ($route) => sprintf('%s/%s', config('app.url'), $route->uri()))
            ->values()
            ->toArray();

        return response()->json($routes)
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
