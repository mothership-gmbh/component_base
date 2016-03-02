<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Tests;

/**
 * Class ExceptionTest.
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @author    Maurizio Brioschi <brioschi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @group Mothership
     * @group Mothership_Exception
     * @group Mothership_Exception_1
     */
    public function basic()
    {
        $exception = new \Mothership\Exception\Exception();
        $this->assertTrue($exception instanceof ExceptionAbstract);
    }
}
