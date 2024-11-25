<?php

namespace Atom\Theme\Http\Controllers\Api;

use Atom\Core\Models\WebsiteHomeCategory;
use Atom\Theme\Http\Resources\WebsiteHomeCategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomeCategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! config('theme.api.home_category_endpoint_enabled')) {
            return response()->json(['error' => 'This endpoint is disabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        $categories = WebsiteHomeCategory::with('children')
            ->where('permission_id', '<=', $request->user()->rank)
            ->whereNull('website_home_category_id')
            ->get();

        return WebsiteHomeCategoryResource::collection($categories)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
