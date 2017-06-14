<?php

namespace nickurt\Akismet;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('nickurt\Akismet\Akismet', function ($app) {
            $akismet = new Akismet;
            $akismet->setApiKey(\Config::get('akismet.api_key'));
            $akismet->setBlogUrl(\Config::get('akismet.blog_url') ?? url('/'));

            return $akismet;
        });

        $this->app->alias('nickurt\Akismet\Akismet', 'Akismet');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/akismet.php' => config_path('akismet.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['nickurt\Akismet\Akismet', 'Akismet'];
    }
}
