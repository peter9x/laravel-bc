<?php

namespace Mupy\BusinessCentral\Facades;

use Illuminate\Support\Facades\Facade;

class BusinessCentral extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'businesscentral';
    }
}
