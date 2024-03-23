<?php

namespace nickurt\Akismet;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../src/Resources/Lang', 'akismet');

        $this->publishes([
            __DIR__.'/../config/akismet.php' => config_path('akismet.php'),
            __DIR__.'/../src/Resources/Lang' => resource_path('lang/vendor/akismet'),
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

            $akismet->setUserIp(\Request::getClientIp());
            $akismet->setUserAgent(\Request::server('HTTP_USER_AGENT'));
            $akismet->setReferrer(\URL::previous());
            $akismet->setPermalink(\Request::url());

            return $akismet;
        });

        $this->app->alias('nickurt\Akismet\Akismet', 'Akismet');
    }
}
