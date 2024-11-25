<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteShopArticle;
use Atom\Core\Models\WebsiteShopCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {
        $categories = WebsiteShopCategory::all();

        $articles = WebsiteShopArticle::with('rank', 'category')
            ->when($request->has('category_id'), fn (Builder $query) => $query->where('website_shop_category_id', $request->get('category_id')))
            ->orderBy('position')
            ->get();

        return view('shop', compact('categories', 'articles'));
    }
}
