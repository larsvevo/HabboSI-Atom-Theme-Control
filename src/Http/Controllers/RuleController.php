<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteRuleCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class RuleController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(): View
    {
        $categories = WebsiteRuleCategory::with('rules')
            ->get();

        return view('rules', compact('categories'));
    }
}
