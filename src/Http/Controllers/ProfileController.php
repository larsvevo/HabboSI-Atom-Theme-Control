<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\User;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(User $user): View
    {
        $user->load('friends', 'guilds', 'badges', 'rooms');

        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('profile', compact('user', 'articles'));
    }
}
