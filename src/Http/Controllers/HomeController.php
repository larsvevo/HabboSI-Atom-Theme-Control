<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\CameraWeb;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {
        $referrals = $request->user()
            ->referrals;

        $friends = $request->user()
            ->friends()
            ->whereRelation('friend', 'online', '1')
            ->get();

        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->get();

        $article = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->first();

        $photos = CameraWeb::whereIn('user_id', $request->user()->friends->pluck('user_two_id'))
            ->latest('id')
            ->limit(4)
            ->where('approved', true)
            ->get();

        return view('home', compact('articles', 'article', 'friends', 'referrals', 'photos'));
    }
}
