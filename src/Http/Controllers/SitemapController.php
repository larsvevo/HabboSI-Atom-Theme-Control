<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\ArrayToXml\ArrayToXml;

class SitemapController extends Controller
{
    /**
     * The routes to not include in the sitemap.
     */
    protected array $protectedRoutes = [
        'admin',
        'sitemap',
        'installation',
        'banned',
        'locale',
        'game',
        'logout',
        'sanctum',
        'create',
        '{',
    ];

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->map(fn (WebsiteArticle $article) => ['url' => route('community.articles.show', $article->slug)]);

        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods()) && in_array('web', $route->middleware()))
            ->filter(fn ($route) => ! collect($this->protectedRoutes)->contains(fn ($protectedRoute) => str_contains($route->uri(), $protectedRoute)))
            ->map(fn ($route) => ['loc' => route($route->getName())])
            ->merge($articles)
            ->values()
            ->toArray();

        $sitemap = ArrayToXml::convert(['url' => $routes], [
            'rootElementName' => 'urlset',
            '_attributes' => [
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
                'xmlns:image' => 'http://www.google.com/schemas/sitemap-image/1.1',
            ],
        ], true, 'UTF-8');

        return response($sitemap)
            ->header('Content-Type', 'application/xml');
    }
}
