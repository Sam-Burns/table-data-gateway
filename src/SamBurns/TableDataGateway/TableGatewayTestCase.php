<?php
namespace SamBurns\TableDataGateway;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Adapter\Platform\Mysql as MysqlPlatform;
use SamBurns\TableDataGateway\Sql;
use SamBurns\TableDataGateway\AbstractTableGateway;
use PHPUnit_Framework_MockObject_MockObject as Mock;

abstract class TableGatewayTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tableName;

    /** @var string */
    protected $tableGatewayClassname;

    /** @var AbstractTableGateway|AbstractTableGateway  (partial mock) */
    protected $tableGateway;

    /** @var Adapter */
    protected $dbAdapter;

    /** @var Sql|Mock */
    protected $sql;

    /** @var Select */
    protected $select;

    /** @var Insert */
    protected $insert;

    /** @var Delete */
    protected $delete;

    /** @var Update */
    protected $update;

    /**
     * Partial mock object
     * @var MysqlPlatform|Mock
     */
    protected $mysqlPlatform;

    public function setUp()
    {
        $this->select = new Select($this->tableName);
        $this->insert = new Insert($this->tableName);
        $this->delete = new Delete($this->tableName);
        $this->update = new Update($this->tableName);

        $this->mysqlPlatform = $this->getMysqlPlatform();

        $this->sql = $this->getMockSql();

        $this->dbAdapter = $this->getSimpleMock('\Zend\Db\Adapter\Adapter');

        $this->tableGateway = $this->getMockTableGateway();

        $mockResultSet = $this->getSimpleMock('\Zend\Db\ResultSet\ResultSet');

        $this->tableGateway
            ->expects($this->any())
            ->method('executeSelect')
            ->will($this->returnValue($mockResultSet));
    }

    /**
     * @return AbstractTableGateway|Mock
     */
    protected function getMockTableGateway()
    {
        return  $this->getMock(
            $this->tableGatewayClassname,
            array('executeSelect', 'executeInsert', 'executeDelete', 'executeUpdate', 'getUniqueIdentifier'),
            array($this->dbAdapter, $this->sql),
            '',
            true,
            true
        );
    }

    /**
     * @return \Zend\Db\Sql\Sql (mock)
     */
    protected function getMockSql()
    {
        $sql = $this->getSimpleMock(Sql::class);

        // Returning select object
        $defaultSelectObject = $this->select;

        $returnSelect =
            function ($tableName = '') use ($defaultSelectObject) {
                if (!$tableName) {
                    return $defaultSelectObject;
                } else {
                    return new Select($tableName);
                }
            };

        $sql->expects($this->any())
            ->method('select')
            ->will($this->returnCallback($returnSelect));

        $sql->expects($this->any())
            ->method('insert')
            ->will($this->returnValue($this->insert));

        // Returning delete object
        $sql->expects($this->any())
            ->method('delete')
            ->will($this->returnValue($this->delete));

        // Returning delete object
        $sql->expects($this->any())
            ->method('update')
            ->will($this->returnValue($this->update));

        // Retrieving table name
        $sql->expects($this->any())
            ->method('getTable')
            ->will($this->returnValue($this->tableName));

        return $sql;
    }

    /**
     * @return MysqlPlatform (partial mock)
     */
    protected function getMysqlPlatform()
    {
        $platform = $this->getMock('\Zend\Db\Adapter\Platform\Mysql', array('quoteValue'), array(), '', false, false);

        $quoteValue =
            function ($valueToQuote) {
                return '\'' . str_replace('\'', '\\' . '\'', $valueToQuote) . '\'';
            };

        $platform
            ->expects($this->any())
            ->method('quoteValue')
            ->with($this->anything())
            ->will($this->returnCallback($quoteValue));

        return $platform;
    }

    /**
     * @param string $expectedSql
     */
    protected function assertTableGatewayLastSqlSelect($expectedSql)
    {
        $actualSql = $this->select->getSqlString($this->mysqlPlatform);
        $this->assertSqlEquals($expectedSql, $actualSql);
    }

    /**
     * @param string $expectedSql
     */
    protected function assertTableGatewayLastSqlDelete($expectedSql)
    {
        $actualSql = $this->delete->getSqlString($this->mysqlPlatform);
        $this->assertSqlEquals($expectedSql, $actualSql);
    }

    /**
     * @param string $expectedSql
     */
    protected function assertTableGatewayLastSqlInsert($expectedSql)
    {
        $actualSql = $this->insert->getSqlString($this->mysqlPlatform);
        $this->assertSqlEquals($expectedSql, $actualSql);
    }

    /**
     * @param string $expectedSql
     */
    protected function assertTableGatewayLastSqlUpdate($expectedSql)
    {
        $actualSql = $this->update->getSqlString($this->mysqlPlatform);
        $this->assertSqlEquals($expectedSql, $actualSql);
    }

    /**
     * @param string $expectedSql
     * @param string $actualSql
     */
    private function assertSqlEquals($expectedSql, $actualSql)
    {
        $expectedSqlCompressedWhitespace = $this->compressWhitespace($expectedSql);
        $actualSqlCompressedWhitespace   = $this->compressWhitespace($actualSql);
        $this->assertEquals($expectedSqlCompressedWhitespace, $actualSqlCompressedWhitespace, 'Failed asserting that SQL is equal');
    }

    /**
     * @param  string $sql
     * @return string
     */
    private function compressWhitespace($sql)
    {
        $sql = preg_replace('/\s+/', ' ', $sql);
        $sql = preg_replace('/^ /', '', $sql);
        $sql = preg_replace('/ $/', '', $sql);
        $sql = preg_replace('/\b(DELETE|SELECT|FROM|INSERT|INTO|VALUES|UPDATE|SET|WHERE|HAVING|ORDER BY|AND|INNER JOIN|LEFT JOIN|RIGHT JOIN)\b/', "\n$1", $sql);
        $sql = preg_replace('/\( /', "(", $sql);
        $sql = preg_replace('/ \)/', ")", $sql);

        return $sql;
    }

    /**
     * @param string $classname
     * @return Mock
     */
    public function getSimpleMock($classname)
    {
        return $this->getMock($classname, array(), array(), '', false, false);
    }
}
