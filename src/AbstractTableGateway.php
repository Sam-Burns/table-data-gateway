<?php
namespace SamBurns\TableDataGateway;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;

abstract class AbstractTableGateway
{
    /** @var string */
    protected $tableName;

    /** @var TableGateway */
    private $gateway;

    /** @var Adapter */
    private $adapter;

    /** @var Sql */
    private $sql;

    /**
     * @param Adapter  $adapter
     * @param Sql|null $sql
     */
    public function __construct(Adapter $adapter, Sql $sql = null)
    {
        $this->adapter = $adapter;
        $this->sql     = $sql;
    }

    /**
     * @return Select
     */
    protected function getSelect()
    {
        return $this->getGateway()->getSql()->select();
    }

    /**
     * @return Update
     */
    protected function getUpdate()
    {
        return $this->getGateway()->getSql()->update();
    }

    /**
     * @return Delete
     */
    protected function getDelete()
    {
        return $this->getGateway()->getSql()->delete();
    }

    /**
     * @return Insert
     */
    protected function getInsert()
    {
        return $this->getGateway()->getSql()->insert();
    }

    /**
     * @return TableGateway
     */
    protected function getGateway()
    {
        if (!$this->gateway) {
            $this->gateway = new TableGateway($this->tableName, $this->adapter, null, null, $this->sql);
        }

        return $this->gateway;
    }

    /**
     * Marked as protected for partial mocking - use outside this class not recommended.
     *
     * @param  Select            $select
     * @return ResultSet
     * @throws \RuntimeException
     */
    protected function executeSelect(Select $select)
    {
        return $this->getGateway()->selectWith($select);
    }

    /**
     * @param  Select            $select
     * @return array
     * @throws \RuntimeException
     */
    protected function selectOne(Select $select)
    {
        $results = $this->selectAll($select);

        return reset($results);
    }

    /**
     * @param  Select            $select
     * @return array
     * @throws \RuntimeException
     */
    protected function selectAll(Select $select)
    {
        $results = $this->executeSelect($select)->toArray();

        return is_array($results) ? $results : array();
    }

    /**
     * @param  Delete            $delete
     * @return int               Affected rows
     * @throws \RuntimeException
     */
    protected function executeDelete(Delete $delete)
    {
        return $this->getGateway()->deleteWith($delete);
    }

    /**
     * @param  Insert            $insert
     * @return int               Affected rows
     * @throws \RuntimeException
     */
    protected function executeInsert(Insert $insert)
    {
        return $this->getGateway()->insertWith($insert);
    }

    /**
     * @param  Update            $update
     * @return int               Affected rows
     * @throws \RuntimeException
     */
    protected function executeUpdate(Update $update)
    {
        return $this->getGateway()->updateWith($update);
    }

    /**
     * @param  int|string $id
     * @return int        Affected rows
     */
    public function delete($id)
    {
        $delete = $this->getGateway()->getSql()->delete(); /* @var $delete Delete */

        $tableName = $this->tableName;

        $delete->where(
            function (Where $where) use ($id, $tableName) {
                $where->equalTo($tableName . '.id', $id);
            }
        );

        return $this->executeDelete($delete);
    }
}
