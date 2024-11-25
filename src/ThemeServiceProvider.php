<?php

namespace Atom\Theme;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Srmklive\PayPal\Services\PayPal;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            path: __DIR__.'/../config/theme.php',
            key: 'theme'
        );

        $this->loadViewsFrom(
            path: __DIR__.'/../resources/views',
            namespace: 'theme',
        );

        $this->loadRoutesFrom(
            path: __DIR__.'/../routes/web.php'
        );

        $this->loadRoutesFrom(
            path: __DIR__.'/../routes/api.php'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PayPal::class, function () {
            $provider = new PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            return $provider;
        });

        $this->setupViews();
    }

    /**
     * Setup views.
     */
    protected function setupViews(): void
    {
        try {
            $settings = DB::table('website_settings')
                ->pluck('value', 'key');

            $onlineUsers = DB::table('users')
                ->where('online', '1')
                ->count();

            $this->loadJsonTranslationsFrom(
                resource_path(sprintf('themes/%s/lang', $settings->get('theme', 'atom'))),
                'theme',
            );

            View::share('settings', $settings);

            View::share('online', $onlineUsers);
        } catch (\Throwable $e) {
            //
        }
    }
}
