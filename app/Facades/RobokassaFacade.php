<?php


namespace App\Facades;
use \Illuminate\Support\Facades\Facade;

class RobokassaFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Robokassa';
    }
}
