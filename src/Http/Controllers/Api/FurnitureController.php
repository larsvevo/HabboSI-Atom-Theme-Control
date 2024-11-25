<?php

namespace Atom\Theme\Http\Controllers\Api;

use Atom\Core\Models\CatalogItem;
use Atom\Theme\Http\Resources\FurnitureResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FurnitureController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! config('theme.api.furniture_endpoint_enabled')) {
            return response()->json(['error' => 'This endpoint is disabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        $items = CatalogItem::with('itemBase', 'itemBase.items.user', 'itemBase.furnitureData')
            ->whereHas('itemBase')
            ->whereHas('itemBase.items')
            ->whereHas('itemBase.furnitureData')
            ->whereHas('itemBase.items.user', fn ($query) => $query->where('rank', '<', 4))
            ->where('club_only', '1')
            ->orderBy('cost_credits', 'DESC')
            ->get()
            ->unique('item_ids');

        return FurnitureResource::collection($items)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
