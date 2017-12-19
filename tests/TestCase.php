<?php

namespace Firstwap\SmsApiAdmin\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Description of TestCase
 *
 * @author muhammad
 */
class TestCase extends BaseTestCase
{

    /**
     * Get private or protected method reflection
     * 
     * @param  mixed 	$object    		An Instance of class
     * @param  string 	$name      		Name of method
     * @return ReflectionClass
     */
    public static function getMethod($obj, $name)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Call a private or protected method from a class
     *
     * @param  mixed 	$object    		An Instance of class
     * @param  string 	$name      		Name of method
     * @param  array  	$parameter 		Parameters for the method
     * @return mixed
     */
    public static function callMethod($object, $name, $parameter = [])
    {
        $method = self::getMethod($object, $name);

        return $method->invokeArgs($object, $parameter);
    }

}
