<?php

namespace nickurt\Akismet;

/**
 * @see \nickurt\Akismet\Akismet
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'Akismet';
    }
}
