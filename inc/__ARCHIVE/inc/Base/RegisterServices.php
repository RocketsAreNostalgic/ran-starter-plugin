<?php

/**
 * @package  RanPlugin
 *
 * Methods for instatinting service classes.
 *
 */

namespace Inc\Base;

class RegisterServices
{

    public function __construct()
    {
        // prevent instantiation
    }

    /**
     * Loop through and initialize service classes by calling their register() method if it exists.
     *
     * @param  array $services
     *
     * @return void
     */
    public static function registerServices(array $services)
    {

        foreach ($services as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize a class
     *
     * @param  class $class    class from an array of Services
     * @return class instance  new instance of the given class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
