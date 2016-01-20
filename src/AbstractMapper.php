<?php
namespace SamBurns\TableDataGateway;

abstract class AbstractMapper
{
    /** @var AbstractTableGateway */
    protected $tableGateway;

    /** @var string */
    protected $modelClassname;

    /**
     * @param  array    $resultSet
     * @return object[]
     */
    protected function buildModelsFromResultSet($resultSet)
    {
        $models = array();

        foreach ($resultSet as $key => $row) {
            $models[] = $this->buildModelFromRow($row);
            unset($resultSet[$key]); // for saving memory
        }

        return $models;
    }

    /**
     * @param  array|null  $resultSet
     * @return object|null
     */
    protected function buildOneModelFromResultSet($resultSet)
    {
        if (is_array($resultSet)) {
            return $this->buildModelFromRow($resultSet);
        }

        return;
    }

    /**
     * @param  array  $rowFromDb
     * @return object
     */
    private function buildModelFromRow($rowFromDb)
    {
        $model = new $this->modelClassname();

        $rowFromDb = $this->applyCustomMappingsForModel($rowFromDb, $model);

        foreach ($rowFromDb as $propertyNameUnderscored => $propertyValue) {
            $propertyNameCamelCase = $this->inflectUnderscoresToCamelCase($propertyNameUnderscored);

            if (property_exists(get_class($model), $propertyNameCamelCase)) {
                $model->$propertyNameCamelCase = $propertyValue;
            }
        }

        return $model;
    }

    /**
     * @param  array  $rowFromDb
     * @param  object $model
     * @return array  Should return $rowFromDb, possibly with some elements removed
     */
    abstract protected function applyCustomMappingsForModel($rowFromDb, $model);

    /**
     * @param  string $propertyNameUnderscored e.g. product_id
     * @return string e.g. productId
     */
    private function inflectUnderscoresToCamelCase($propertyNameUnderscored)
    {
        $propertyNameCamelCase = str_replace('_', ' ', $propertyNameUnderscored);
        $propertyNameCamelCase = ucwords($propertyNameCamelCase);
        $propertyNameCamelCase = str_replace(' ', '', $propertyNameCamelCase);
        $propertyNameCamelCase = lcfirst($propertyNameCamelCase);

        return $propertyNameCamelCase;
    }

    /**
     * @param  string|int  $id
     * @return object|null
     */
    public function findById($id)
    {
        if (null === $id || '' === $id) {
            return;
        }

        $resultSet = $this->tableGateway->findById($id);

        return $this->buildOneModelFromResultSet($resultSet);
    }
}
