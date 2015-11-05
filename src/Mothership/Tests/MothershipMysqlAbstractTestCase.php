<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mothership
 * @package   Mothership_{EXTENSION NAME}
 * @author    Maurizio Brioschi <brioschi@mothership.de>
 * @copyright Copyright (c) 2015 Mothership GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.mothership.de/
 */

namespace Mothership\Tests;

abstract class MothershipMysqlAbstractTestCase extends \PHPUnit_Extensions_Database_TestCase
{

    static private $pdo = null;

    private $conn = null;

    protected $dataset;

    /**
     * Set the dataset file for the test
     * @return mixed
     */
    abstract protected function setYamlDataSet();

    protected function setUp()
    {
        $this->setYamlDataSet();
        $this->getConnection();
        $fixtureDataSet = $this->getDataSet($this->dataset);
        foreach ($fixtureDataSet->getTableNames() as $table) {
            self::$pdo->exec("DROP TABLE IF EXISTS ".$table.";");

            $meta = $fixtureDataSet->getTableMetaData($table);
            $create = "CREATE TABLE IF NOT EXISTS `".$table."`";
            $cols = array();
            foreach ($meta->getColumns() as $col) {
                $cols[] = " `".$col."` VARCHAR(200)";
            }
            $create .= '('.implode(',', $cols).');';
            self::$pdo->exec($create);
        }
        parent::setUp();
    }

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_YamlDataSet($this->dataset);
    }

    public function tearDown() {
        $allTables = $this->getDataSet($this->dataset)->getTableNames();
        foreach ($allTables as $table) {
            // drop table
            $conn = $this->getConnection();
            $pdo = $conn->getConnection();
            $pdo->exec("DROP TABLE IF EXISTS `$table`;");
        }

        parent::tearDown();
    }

    /**
     * call private methods
     *
     * @param object &$object Object
     * @param string $methodName methods
     * @param array $parameters params
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
     * get private property value
     * @param type $object
     * @param type $propertyName
     * @return type
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
