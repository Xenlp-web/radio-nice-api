<?php


namespace App\Facades;

use \Illuminate\Support\Facades\Facade;
class RadioFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Radio';
    }
}
