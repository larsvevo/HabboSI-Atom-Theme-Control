<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteHelpCenterCategory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HelpCentreController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(): View
    {
        $categories = WebsiteHelpCenterCategory::orderBy('position')
            ->get();

        return view('help-center', compact('categories'));
    }
}
