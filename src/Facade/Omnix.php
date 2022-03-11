<?php
namespace omnixdeveloper\LaravelOmnix\Facade;
use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: lee
 * Date: 11/12/2017
 * Time: 3:31 PM
 */
class Omnix extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \omnixdeveloper\LaravelOmnix\Lib\Ethereum::class;
    }
}
