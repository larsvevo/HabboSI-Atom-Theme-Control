<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteRareValueCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class RareValueController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {
        $categories = WebsiteRareValueCategory::with([
            'rareValues' => fn ($query) => $query->where('name', 'like', "%{$request->query('search')}%"),
            'rareValues.item',
        ])
            ->when($request->has('category_id'), fn (Builder $query) => $query->where('id', $request->query('category_id')))
            ->get();

        return view('rare-values', compact('categories'));
    }

    public function value(WebsiteRareValue $value): View
    {
        $items = Item::with(['user:id,username,look'])
            ->where('item_id', $value->item_id)
            ->get();

        $itemsPerUser = $items->groupBy('user_id')->map(function ($group) {
            return [
                'user' => $group->first()->user,
                'item_count' => $group->count(),
            ];
        });

        if ((bool) setting('enable_caching')) {
            Cache::remember('allItems_'.$value->id, setting('cache_timer'), function () use ($items) {
                return $items;
            });
        }

        return view('value', [
            'value' => $value,
            'items' => $itemsPerUser,
        ]);
    }
}
