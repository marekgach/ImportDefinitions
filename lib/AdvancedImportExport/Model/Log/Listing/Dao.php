<?php

namespace AdvancedImportExport\Model\Log\Listing;

use AdvancedImportExport\Model\Log;
use Pimcore\Model\Listing;
use Pimcore\Tool;

/**
 * Class Dao
 * @package AdvancedImportExport\Model\Log\Listing
 */
class Dao extends Listing\Dao\AbstractDao
{
    /**
     * @var string
     */
    protected $tableName = 'advancedimportexport_log';

    /**
     * Get tableName, either for localized or non-localized data.
     *
     * @return string
     *
     * @throws \Exception
     * @throws \Zend_Exception
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    /**
     * get select query.
     *
     * @return \Zend_Db_Select
     *
     * @throws \Exception
     */
    public function getQuery()
    {

        // init
        $select = $this->db->select();

        // create base
        $field = $this->getTableName().'.id';
        $select->from(
            [$this->getTableName()], [
                new \Zend_Db_Expr(sprintf('SQL_CALC_FOUND_ROWS %s as id', $field, 'o_type')),
            ]
        );

        // add condition
        $this->addConditions($select);

        // group by
        $this->addGroupBy($select);

        // order
        $this->addOrder($select);

        // limit
        $this->addLimit($select);

        return $select;
    }

    /**
     * Loads objects from the database.
     *
     * @return Log[]
     */
    public function load()
    {
        // load id's
        $list = $this->loadIdList();

        $objects = array();
        foreach ($list as $o_id) {
            if ($object = Log::getById($o_id)) {
                $objects[] = $object;
            }
        }

        $this->model->setData($objects);

        return $objects;
    }

    /**
     * Loads a list for the specicifies parameters, returns an array of ids.
     *
     * @return array
     * @throws \Exception
     */
    public function loadIdList()
    {
        try {
            $query = $this->getQuery();
            $objectIds = $this->db->fetchCol($query, $this->model->getConditionVariables());
            $this->totalCount = (int) $this->db->fetchOne('SELECT FOUND_ROWS()');

            return $objectIds;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Count.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function getCount()
    {
        $amount = (int) $this->db->fetchOne('SELECT COUNT(*) as amount FROM '.$this->getTableName().$this->getCondition().$this->getOffsetLimit(), $this->model->getConditionVariables());

        return $amount;
    }

    /**
     * Get Total Count.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function getTotalCount()
    {
        $amount = (int) $this->db->fetchOne('SELECT COUNT(*) as amount FROM '.$this->getTableName().$this->getCondition(), $this->model->getConditionVariables());

        return $amount;
    }
}