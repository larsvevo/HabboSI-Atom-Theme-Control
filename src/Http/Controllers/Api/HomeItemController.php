<?php

namespace Atom\Theme\Http\Controllers\Api;

use Atom\Core\Models\User;
use Atom\Core\Models\WebsiteHomeItem;
use Atom\Rcon\Services\RconService;
use Atom\Theme\Http\Resources\WebsiteHomeItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HomeItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user): JsonResponse
    {
        if (! config('theme.api.home_item_endpoint_enabled')) {
            return response()->json(['error' => 'This endpoint is disabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        $items = match ($request->get('type')) {
            'webstore' => WebsiteHomeItem::where('website_home_category_id', $request->get('category_id'))
                ->where('permission_id', '<=', $request->user()->rank)
                ->orderBy('name')
                ->get(),

            'inventory' => $user->inventoryItems()
                ->where('website_home_category_id', $request->get('category_id'))
                ->orWhereRelation('category', 'website_home_category_id', $request->get('category_id'))
                ->wherePivot('user_id', $request->user()->id)
                ->orderBy('name')
                ->get(),

            default => $user->activeItems()
                ->get(),
        };

        return WebsiteHomeItemResource::collection($items)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RconService $rconService, Request $request): JsonResponse
    {
        $item = WebsiteHomeItem::findOrFail($request->get('item_id'));

        if ($item->maximum_purchases !== -1) {
            abort_if($request->user()->inventoryItems->where('id', $request->get('item_id'))->count() >= $item->maximum_purchases, JsonResponse::HTTP_BAD_REQUEST, 'You have reached the maximum amount of this item.');
        }

        abort_if($request->user()->credits < $item->price, JsonResponse::HTTP_BAD_REQUEST);

        if (is_null($rconService->giveCredits($request->user()->id, -$item->price))) {
            $request->user()->update(['credits' => $request->user()->credits - $item->price]);
        }

        foreach (range(1, $item->count) as $index) {
            $request->user()
                ->inventoryItems()
                ->attach($request->get('item_id'), ['left' => null, 'top' => null, 'z' => 1, 'data' => json_encode((object) [])]);
        }

        return $this->index($request, $request->user());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $existingItems = $request->user()
            ->homeItems()
            ->get();

        $request->user()
            ->update(['home_background' => str_contains('home-bg.gif', $request->get('background')) ? str_replace('/storage/', '', $request->get('background')) : $request->get('background')]);

        DB::table('user_website_home_item')
            ->where('user_id', $request->user()->id)
            ->update(['left' => null, 'top' => null]);

        foreach ($request->get('items') as $item) {
            if (! $existingItems->where('pivot.id', Arr::get($item, 'id'))->first()) {
                continue;
            }

            $item['data'] = json_encode((object) $item['data']);

            DB::table('user_website_home_item')
                ->where('user_id', $request->user()->id)
                ->where('id', Arr::get($item, 'id'))
                ->update(Arr::only($item, ['left', 'top', 'z', 'data']));
        }

        return $this->index($request, $request->user());
    }
}
