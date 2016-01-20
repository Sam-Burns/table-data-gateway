<?php
namespace SamBurns\TableDataGateway\Test\Fixtures;

use SamBurns\TableDataGateway\AbstractMapper;

class ExampleConcreteMapper extends AbstractMapper
{
    /** @var string */
    protected $modelClassname = ExampleModel::class;

    /** @var ExampleConcreteTableGateway */
    protected $tableGateway;

    /**
     * @param ExampleConcreteTableGateway $tableGateway
     */
    public function __construct(ExampleConcreteTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @return array
     */
    public function getSomeResults()
    {
        $rowset = $this->tableGateway->findBySomeField('asdf');

        return $this->buildModelsFromResultSet($rowset);
    }

    /**
     * @param  array        $rowFromDb
     * @param  ExampleModel $model
     * @return array
     */
    protected function applyCustomMappingsForModel($rowFromDb, $model)
    {
        $model->name = $rowFromDb['name_en'];

        unset($rowFromDb['name_en'], $rowFromDb['name_fr']);

        return $rowFromDb;
    }
}
