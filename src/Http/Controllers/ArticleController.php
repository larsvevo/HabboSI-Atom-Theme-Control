<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteArticle;
use Atom\Theme\Http\Requests\ReactionUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->paginate(8);

        return view('articles.index', compact('articles'));
    }

    /**
     * Display the specified resource.
     */
    public function show(WebsiteArticle $article): View
    {
        $article->load('user', 'comments.user', 'reactions.user');

        $articles = WebsiteArticle::with('user', 'comments.user', 'reactions.user')
            ->where('is_published', true)
            ->latest('id')
            ->paginate(8);

        return view('articles.show', compact('article', 'articles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReactionUpdateRequest $request, WebsiteArticle $article): RedirectResponse
    {
        $reaction = $article->reactions()
            ->where('user_id', $request->user()->id)
            ->where('reaction', $request->get('reaction'));

        match ($reaction->exists()) {
            true => $reaction->delete(),
            false => $article->reactions()
                ->create([
                    'user_id' => $request->user()->id,
                    'reaction' => $request->get('reaction'),
                    'active' => true,
                ]),
        };

        return redirect()
            ->back();
    }
}
