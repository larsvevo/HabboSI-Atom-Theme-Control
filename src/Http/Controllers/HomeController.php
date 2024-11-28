<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\CameraWeb;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Atom\Core\Models\Room;

class HomeController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {

        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->get();

        $article = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->first();

            
        $topRooms = Room::orderByDesc('users')
            ->select('name', 'users', 'users_max', 'owner_name')
            ->limit(4)
            ->get();

        return view('home', compact('articles', 'article','topRooms'));
    }
}
