<?php
class Core_Model_Mapper_Batch
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Core_Model_Mapper_Batch
     */
    public function setDbTable ($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (! $dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    /**
     * Get registered Zend_Db_Table instance
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Core_Model_DbTable_Batch');
        }
        return $this->_dbTable;
    }
    /**
     * Fetches Batch details
     * 
     * @param integer $batch_id
     */
    public function fetchInfo ($batch_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $batch_table = $db_table->info('name');
        $required_cols = array('batch_id', 'department_id', 'programme_id', 
        'batch_start', 'batch_number', 'is_active');
        $select = $adapter->select()
            ->from($batch_table, $required_cols)
            ->where('batch_id = ?', $batch_id);
        $batch_info = array();
        $batch_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        if (empty($batch_info)) {
            return false;
        } else {
            return $batch_info[$batch_id];
        }
    }
    /**
     * 
     * fetches the Batch Ids
     * @param bool $batch_start optional
     * @param bool $department_id optional
     * @param bool $programme_id optional
     * @return array
     */
    public function fetchBatchIds ($batch_start = null, $department_id = null, 
    $programme_id = null)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $batch_table = $db_table->info('name');
        $required_cols = array('batch_id');
        $select = $adapter->select()->from($batch_table, $required_cols);
        if (isset($batch_start)) {
            $select->where('batch_start = ?', $batch_start);
        }
        if (isset($department_id)) {
            $select->where('department_id = ?', $department_id);
        }
        if (isset($programme_id)) {
            $select->where('programme_id = ?', $programme_id);
        }
        $batch_ids = array();
        $r = $select->__toString();
        $batch_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $batch_ids;
    }
    public function batchExistCheck ($batch_id)
    {
        $batches = $this->getDbTable()->find($batch_id);
        if (0 == count($batches)) {
            return false;
        } else {
            return true;
        }
    }
    public function save ($prepared_data)
    {
        $dbtable = $this->getDbTable();
        return $dbtable->insert($prepared_data);
    }
    public function update ($prepared_data, $batch_id)
    {
        $dbtable = $this->getDbTable();
        $where = 'batch_id = ' . $batch_id;
        return $dbtable->update($prepared_data, $where);
    }
}