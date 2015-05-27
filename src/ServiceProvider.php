<?php 

namespace nickurt\Akismet;

use \nickurt\Akismet\ProviderFactory;

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
        $this->app->bindShared('Akismet', function($app)
        {
            return new \nickurt\Akismet\Akismet(
                \Config::get('akismet')['api_key'],
                \Config::get('akismet')['blog_url']
            );
        });
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
        ]);
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Akismet'];
    }
}
