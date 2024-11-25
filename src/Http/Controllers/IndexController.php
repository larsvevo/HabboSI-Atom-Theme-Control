<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\CameraWeb;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(): View
    {
        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->limit(4)
            ->get();

        $photos = CameraWeb::with('user')
            ->latest('id')
            ->where('approved', true)
            ->limit(4)
            ->get();

        return view('index', compact('articles', 'photos'));
    }
}
