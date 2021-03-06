<?php
class Acad_Model_Mapper_Member_Qualification
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Member_Qualification
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
            $this->setDbTable('Acad_Model_DbTable_MemberQualification');
        }
        return $this->_dbTable;
    }
    /**
     * Fetches Member Qualification ids
     * 
     * @param integer $member_id
     */
    public function fetchQualificationIds ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $member_qualification_table = $db_table->info('name');
        $required_cols = array('qualification_id');
        $select = $adapter->select()
            ->from($member_qualification_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $student_info = array();
        $student_info = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (empty($student_info)) {
            return false;
        } else {
            return $student_info;
        }
    }
    public function save ($data)
    {
        $dbtable = $this->getDbTable();
        return $dbtable->insert($data);
    }
    public function delete ($member_id, $qualification_id)
    {
        $where1 = 'member_id = ' . $member_id;
        $where2 = 'qualification_id = ' . $qualification_id;
        $dbtable = $this->getDbTable();
        return $dbtable->delete(array($where1, $where2));
    }
    //no need for update function
}