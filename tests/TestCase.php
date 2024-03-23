<?php

namespace nickurt\Akismet\tests;

use Illuminate\Foundation\Application;
use nickurt\Akismet\Facade;
use nickurt\Akismet\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('akismet.api_key', 'abcdefghijklmnopqrstuvwxyz');
        $app['config']->set('akismet.blog_url', 'http://akismet.local');
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Akismet' => Facade::class,
        ];
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
