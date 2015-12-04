<?php namespace Dgeorgiev\Epay\Facades;
/**
 * Class Facade
 * @package Dgeorgiev\Epay\Facades
 * @see Dgeorgiev\Epay\Epay
 */
use Illuminate\Support\Facades\Facade;

class Epay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Dgeorgiev\Epay\Epay';
    }

}
