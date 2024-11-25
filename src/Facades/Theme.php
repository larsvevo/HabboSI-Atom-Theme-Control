<?php

namespace Atom\Theme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Atom\Theme\Theme
 */
class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Atom\Theme\Theme::class;
    }
}
