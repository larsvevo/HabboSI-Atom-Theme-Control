<?php

namespace Atom\Theme\Http\Middleware;

use Atom\Core\Models\WebsiteSetting;
use Closure;
use Illuminate\Http\Request;
use Qirolab\Theme\Theme;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $theme = WebsiteSetting::firstOrCreate(
            ['key' => 'theme'],
            ['value' => 'atom', 'comment' => 'Specifies the active CMS theme'],
        );

        if (! $theme->value || $theme->value === '1') {
            $theme->update(['value' => 'atom']);
        }

        if (! Theme::active() || $theme->value !== Theme::active()) {
            Theme::set($theme->value);
        }

        return $next($request);
    }
}
