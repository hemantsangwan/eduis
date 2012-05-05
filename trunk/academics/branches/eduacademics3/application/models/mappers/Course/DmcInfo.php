<?php
class Acad_Model_Mapper_Course_DmcInfo
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Course_DmcInfo
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
            $this->setDbTable('Acad_Model_DbTable_DmcInfo');
        }
        return $this->_dbTable;
    }
    /**
     * Fetches Dmc Info
     * 
     * @param integer $dmc_info_id
     */
    public function fetchInfo ($dmc_info_id, $dmc_id = null)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $dmc_info_table = $db_table->info('name');
        $select = $adapter->select();
        if (isset($dmc_info_id)) {
            $required_cols = array('dmc_info_id', 'dmc_id', 'is_considered', 
            'result_type_id', 'class_id', 'member_id', 'roll_no', 'examination', 
            'custody_date', 'is_granted', 'grant_date', 'recieveing_date', 
            'is_copied', 'dispatch_date', 'marks_obtained', 'total_marks', 
            'scaled_marks', 'percentage');
            $select->where('dmc_info_id = ?', $dmc_info_id);
            $dmc_info = array();
            $dmc_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
            return $dmc_info[$dmc_info_id];
        }
        if (isset($dmc_id)) {
            $required_cols = array('dmc_info_id');
            $select->from($dmc_info_table, $required_cols)->where('dmc_id = ?', 
            $dmc_id);
            return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        }
    }
    public function fetchDmcInfoIds ($member_id, $class_id = null, 
    $result_type_id = null, $all = null, $is_considered = null, $ordered_by_date = null)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $dmc_info_table = $db_table->info('name');
        $dmc_info_ids = array();
        $required_cols = array('dmc_info_id');
        $select = $adapter->select()
            ->from($dmc_info_table, $required_cols)
            ->where('member_id = ?', $member_id);
        if (isset($class_id)) {
            $select->where('class_id = ?', $class_id);
        }
        if (isset($result_type_id)) {
            $select->where('result_type_id = ?', $result_type_id);
        }
        if (isset($result_type_id)) {
            $select->where('is_considered = ?', $is_considered);
        }
        if (isset($ordered_by_date)) {
            $required_cols[] = 'dispatch_date';
            $select->order('dispatch_date desc');
            $dmc_info_ids = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
            return $dmc_info_ids;
        }
        $dmc_info_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $dmc_info_ids;
    }
    public function fetchResultTypes ()
    {
        $db_table = new Acad_Model_DbTable_ResultType();
        $adapter = $db_table->getAdapter();
        $result_type_table = $db_table->info('name');
        $required_cols = array('result_type_id', 'result_type_name');
        $select = $adapter->select()->from($result_type_table, $required_cols);
        $result_types = array();
        return $select->query()->fetchAll();
    }
    public function save ($prepared_data)
    {
        $dbtable = $this->getDbTable();
        return $dbtable->insert($prepared_data);
    }
    public function update ($prepared_data, $dmc_info_id)
    {
        $dbtable = $this->getDbTable();
        $where = 'dmc_info_id = ' . $dmc_info_id;
        return $dbtable->update($prepared_data, $where);
    }
}
?>