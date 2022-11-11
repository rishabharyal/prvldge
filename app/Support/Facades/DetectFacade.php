<?php


namespace App\Support\Facades;


use Illuminate\Support\Facades\Facade;

class DetectFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor():string
    {
        return 'detect';
    }
}
