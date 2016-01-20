<?php
namespace SamBurns\TableDataGateway;

use Zend\Db\Sql\Sql as ZendSql;

class Sql extends ZendSql
{
    /**
     * @param  string $table
     * @return Sql
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }
}
