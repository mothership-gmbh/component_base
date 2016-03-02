<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Tests;

/**
 * Class Mothership\Tests\TraitBase.
 *
 * @category  Mothership
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
trait TraitBase
{
    /**
     * Helper to invoke private methods. You should avoid to build tests around invoking private methods.
     * Instead it might be better to test coverage by executing the caller.
     *
     * Testing private methods intensely might also be a sign of bad code design.
     *
     * @param object &$object    Object
     * @param string $methodName methods
     * @param array  $parameters params
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Helper to retreive a private property. Also try to avoid using this method if possible.
     * If there is a value you want to inspect, think about creating a proper getter
     *
     * @param string $object
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getPropertyValue(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * @param $object
     * @param $propertyName
     *
     * @return string
     */
    protected function getPropertyClass(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return get_class($property->getValue($object));
    }
}
