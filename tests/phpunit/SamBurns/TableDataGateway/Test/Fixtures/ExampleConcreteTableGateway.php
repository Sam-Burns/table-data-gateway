<?php
namespace SamBurns\TableDataGateway\Test\Fixtures;

use SamBurns\TableDataGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Where;

class ExampleConcreteTableGateway extends AbstractTableGateway
{
    /** @var string */
    protected $tableName = 'countries';

    /**
     * @param  int   $countryId
     * @return array
     */
    public function findById($countryId)
    {
        /* @var $select Select */
        $select = $this->getSelect();

        $select->columns(
            array(
                'id'      => 'id',
                'name_en' => 'country_name',
                'name_fr' => 'le_country_name',
            ),
            true
        );

        $select->where(
            function (Where $where) use ($countryId) {
                $where->equalTo('countries.id', $countryId);
            }
        );

        return $this->selectOne($select);
    }

    /**
     * @param  string $someCriterion
     * @return array
     */
    public function findBySomeField($someCriterion)
    {
        /* @var $select Select */
        $select = $this->getSelect();

        $select->columns(
            array(
                'id'      => 'id',
                'name_en' => 'country_name',
                'name_fr' => 'le_country_name',
            ),
            true
        );

        $select->where(
            function (Where $where) use ($someCriterion) {
                $where->equalTo('countries.something', $someCriterion);
            }
        );

        return $this->selectAll($select);
    }

    /**
     * @param  string $countryName
     * @return int    Affected rows
     */
    public function deleteByName($countryName)
    {
        /* @var $delete Delete */
        $delete = $this->getDelete();

        $delete->where(
            function (Where $where) use ($countryName) {
                $where->equalTo('countries.country_name', $countryName);
            }
        );

        return $this->executeDelete($delete);
    }
}
