<?php

namespace Shahadh\TemporaryLinks\Facades;

use Illuminate\Support\Facades\Facade;

class TemporaryLink extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'temporary-link';
    }
}