<?php

use nickurt\Akismet\Akismet;

if (! function_exists('akismet')) {
    function akismet()
    {
        return app(Akismet::class);
    }
}