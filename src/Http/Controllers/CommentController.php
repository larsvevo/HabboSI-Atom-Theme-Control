<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteArticle;
use Atom\Theme\Http\Requests\CommentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(CommentRequest $request, WebsiteArticle $websiteArticle): RedirectResponse
    {
        $websiteArticle->comments()
            ->create([
                'user_id' => $request->user()->id,
                'comment' => $request->get('comment'),
            ]);

        return redirect()->back();
    }
}
