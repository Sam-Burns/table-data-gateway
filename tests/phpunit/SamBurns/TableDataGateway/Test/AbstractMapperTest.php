<?php
namespace SamBurns\TableDataGateway\Test;

use SamBurns\TableDataGateway\Test\Fixtures\ExampleConcreteTableGateway;
use SamBurns\TableDataGateway\Test\Fixtures\ExampleConcreteMapper;
use SamBurns\TableDataGateway\Test\Fixtures\ExampleModel;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use PHPUnit_Framework_TestCase as TestCase;

class AbstractMapperTest extends TestCase
{
    /** @var ExampleConcreteMapper */
    private $exampleMapper;

    /** @var ExampleConcreteTableGateway|Mock */
    private $exampleTableGateway;

    public function setUp()
    {
        $this->exampleTableGateway = $this->getSimpleMock(ExampleConcreteTableGateway::class);
        $this->exampleMapper       = new ExampleConcreteMapper($this->exampleTableGateway);
    }

    /**
     * @param  string $classname
     * @return Mock
     */
    protected function getSimpleMock($classname)
    {
        return $this->getMock($classname, array(), array(), '', false, false, true);
    }

    /**
     * @return array
     */
    private function getSampleData()
    {
        return array(
            'id'      => '234',
            'name_fr' => 'Le name de ze country',
            'name_en' => 'Country Name',
        );
    }

    public function testQueriesWithOneResult()
    {
        // ARRANGE
        $this->exampleTableGateway
            ->expects($this->once())
            ->method('findById')
            ->with('234')
            ->will($this->returnValue($this->getSampleData()));

        // ACT
        $result = $this->exampleMapper->findById('234'); /* @var ExampleModel $result */

        // ASSERT
        $this->assertInstanceOf(ExampleModel::class, $result);
        $this->assertEquals('234', $result->id);
        $this->assertEquals('Country Name', $result->name);
    }

    public function testQueriesWithMultipleResults()
    {
        // ARRANGE
        $this->exampleTableGateway
            ->expects($this->once())
            ->method('findBySomeField')
            ->will($this->returnValue(array($this->getSampleData())));

        // ACT
        $result = $this->exampleMapper->getSomeResults(); /* @var ExampleModel[] $result */

        // ASSERT
        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(ExampleModel::class, $result[0]);
        $this->assertEquals('234', $result[0]->id);
        $this->assertEquals('Country Name', $result[0]->name);
    }
}
