<?php
class Acad_Model_Mapper_Qualification_Matric
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Qualification_Matric
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
            $this->setDbTable('Acad_Model_DbTable_Matric');
        }
        return $this->_dbTable;
    }
    /**
     * Fetches Matric details of a member
     * 
     * @param integer $member_id
     */
    public function fetchInfo ($member_id)
    {
        $db_table = $this->getDbTable();
        $adapter = $db_table->getAdapter();
        $matric_table = $db_table->info('name');
        $required_cols = array('member_id', 'qualification_id', 'board', 
        'board_roll_no', 'marks_obtained', 'total_marks', 'percentage', 
        'passing_year', 'school_rank', 'remarks', 'institution', 'city_name', 
        'state_name');
        $select = $adapter->select()
            ->from($matric_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $student_info = array();
        $student_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        if (empty($student_info)) {
            return false;
        } else {
            return $student_info[$member_id];
        }
    }
    public function fetchStudents ($exact_property, $property_range)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $stu_table = $db_table->info('name');
        $required_cols = array('member_id');
        $select = $adapter->select()->from($stu_table, $required_cols);
        foreach ($property_range as $key => $range) {
            if (! empty($range['from'])) {
                $select->where("$key >= ?", $range['from']);
            }
            if (! empty($range['to'])) {
                $select->where("$key <= ?", $range['to']);
            }
        }
        foreach ($exact_property as $exact_key => $exact_range) {
            $select->where("$exact_key = ?", $exact_range);
        }
        $member_ids = array();
        $member_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $member_ids;
    }
    public function save ($prepared_data)
    {
        Zend_Registry::get('logger')->debug($prepared_data);
        $dbtable = $this->getDbTable();
        try {
            $row_id = $dbtable->insert($prepared_data);
        } catch (Exception $exception) {
            throw $exception;
        }
    }
    public function update ($prepared_data, $member_id, $qualification_id)
    {
        Zend_Registry::get('logger')->debug($prepared_data);
        $dbtable = $this->getDbTable();
        $where1 = 'member_id = ' . $member_id;
        $where2 = 'qualification_id = ' . $qualification_id;
        return $dbtable->update($prepared_data, array($where1, $where2));
    }
}