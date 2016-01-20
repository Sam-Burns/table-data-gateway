<?php
namespace SamBurns\TableDataGateway\Test;

use SamBurns\TableDataGateway\TableGatewayTestCase;
use SamBurns\TableDataGateway\Test\Fixtures\ExampleConcreteTableGateway;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class AbstractTableGatewayTest extends TableGatewayTestCase
{
    /** @var string */
    protected $tableName = 'countries';

    /** @var string */
    protected $tableGatewayClassname = ExampleConcreteTableGateway::class;

    /** @var ExampleConcreteTableGateway|Mock */
    protected $tableGateway;

    public function testFindByUserId()
    {
        // ARRANGE
        $expectedSql = 'SELECT
                            `countries`.`id` AS `id`,
                            `countries`.`country_name` AS `name_en`,
                            `countries`.`le_country_name` AS `name_fr`
                        FROM `countries`
                        WHERE `countries`.`id` = \'123\'';

        // ACT
        $this->tableGateway->findById('123');

        // ASSERTION
        $this->assertTableGatewayLastSqlSelect($expectedSql);
    }

    public function testDeleteByName()
    {
        // ARRANGE
        $expectedSql = "DELETE FROM `countries` WHERE `countries`.`country_name` = 'France'";

        // ACT
        $this->tableGateway->deleteByName('France'); // If only;

        // ASSERT
        $this->assertTableGatewayLastSqlDelete($expectedSql);
    }

    public function testDelete()
    {
        // ARRANGE
        $expectedSql = "DELETE FROM `countries` WHERE `countries`.`id` = '123'";

        // ACT
        $this->tableGateway->delete('123');

        // ASSERT
        $this->assertTableGatewayLastSqlDelete($expectedSql);
    }
}
