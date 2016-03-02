<?php
/**
 * This file is part of the Mothership GmbH code.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Mothership\Tests;

/**
 * Class DatabaseTestCase.
 *
 * @category  Mothership
 *
 * @author    Don Bosco van Hoi <vanhoi@mothership.de>
 * @copyright 2016 Mothership GmbH
 *
 * @link      http://www.mothership.de/
 * @see       https://phpunit.de/manual/current/en/database.html
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    use TraitBase;

    /**
     * @var \PDO
     */
    private $pdo = null;

    /**
     * @var string
     */
    protected $dataset;

    /**
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     *
     * @link http://someguyjeremy.com/2013/01/database-testing-with-phpunit.html
     */
    public function getConnection()
    {
        if (null === $this->pdo) {
            $this->pdo = new \PDO('sqlite::memory:');

            /*
             *
             */
            $fixtureDataSet = $this->getDataSet();
            foreach ($fixtureDataSet->getTableNames() as $table) {
                $this->pdo->exec('DROP TABLE IF EXISTS ' . $table . ';');

                $meta   = $fixtureDataSet->getTableMetaData($table);
                $create = 'CREATE TABLE IF NOT EXISTS `' . $table . '`';
                $cols   = [];
                foreach ($meta->getColumns() as $col) {
                    $cols[] = ' `' . $col . '` VARCHAR(200)';
                }
                $create .= '(' . implode(',', $cols) . ');';
                $this->pdo->exec($create);
            }
        }

        return $this->createDefaultDBConnection($this->pdo, ':memory:');
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet($this->dataset);
    }
}
