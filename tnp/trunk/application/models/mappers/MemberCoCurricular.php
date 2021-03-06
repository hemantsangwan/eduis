<?php
class Tnp_Model_Mapper_MemberCoCurricular
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Tnp_Model_Mapper_MemberCoCurricular
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
            $this->setDbTable('Tnp_Model_DbTable_CoCurricular');
        }
        return $this->_dbTable;
    }
    /**
     * 
     * @param integer $member_id
     */
    public function fetchInfo ($member_id)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $stu_cc_table = $db_table->info('name');
        $required_cols = array('member_id', 'achievements', 'activities', 
        'hobbies');
        $select = $adapter->select()
            ->from($stu_cc_table, $required_cols)
            ->where('member_id = ?', $member_id);
        $co_curricular_info = array();
        $co_curricular_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        if (empty($co_curricular_info)) {
            return false;
        } else {
            return $co_curricular_info[$member_id];
        }
    }
    public function fetchMemberIds ($achievements = null, $activities = null, 
    $hobbies = null)
    {
        $adapter = $this->getDbTable()->getAdapter();
        $db_table = $this->getDbTable();
        $stu_cc_table = $db_table->info('name');
        $required_cols = array('member_id');
        $select = $adapter->select()->from($stu_cc_table, $required_cols);
        if (! empty($achievements)) {
            $select->where('achievements = ?', $achievements);
        }
        if (! empty($activities)) {
            $select->where('activities = ?', $activities);
        }
        if (! empty($$hobbies)) {
            $select->where('hobbies = ?', $hobbies);
        }
        $member_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return $member_ids;
    }
    public function save ($prepared_data)
    {
        $dbtable = $this->getDbTable();
        return $dbtable->insert($prepared_data);
    }
    public function delete ($member_id)
    {
        $dbtable = $this->getDbTable();
        $where = 'member_id = ' . $member_id;
        return $dbtable->delete($where);
    }
    public function update ($prepared_data, $member_id)
    {
        $dbtable = $this->getDbTable();
        $where = 'member_id = ' . $member_id;
        return $dbtable->update($prepared_data, $where);
    }
}
?>