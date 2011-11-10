<?php
class Acad_Model_Mapper_Exam_Aisse
{
    protected $_table_cols = null;
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    /**
     * @return the $_table_cols
     */
    protected function getTable_cols ()
    {
        if (! isset($this->_table_cols)) {
            $this->setTable_cols();
        }
        return $this->_table_cols;
    }
    /**
     * @param field_type $_table_cols
     */
    protected function setTable_cols ()
    {
        $this->_table_cols = $this->getDbTable()->info('cols');
    }
    /**
     * Specify Zend_Db_Table instance to use for data operations
     * 
     * @param  Zend_Db_Table_Abstract $dbTable 
     * @return Acad_Model_Mapper_Exam_Aisse
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
            $this->setDbTable('Acad_Model_DbTable_Aisse');
        }
        return $this->_dbTable;
    }
    /**
     * 
     * Enter description here ...
     * @param Acad_Model_Exam_Aisse $aisse
     */
    public function fetchMemberExamInfo (Acad_Model_Exam_Aisse $aisse)
    {
        $member_id = $aisse->getMember_id();
        Zend_Registry::get('logger')->debug($member_id);
        $adapter = $this->getDbTable()->getAdapter();
        $required_fields = $this->getTable_cols();
        $select = $adapter->select()
            ->from('matric', $required_fields)
            ->where('member_id = ?', $member_id);
        $member_exam_info = array();
        $member_exam_info = $select->query()->fetchAll(Zend_Db::FETCH_UNIQUE);
        return $member_exam_info[$member_id];
    }
    /**
     * 
     * Enter description here ...
     * @param array $options
     * @param Acad_Model_Exam_Aisse $aisse
     */
    public function save ($options, Acad_Model_Exam_Aisse $aisse = null)
    {
        $all_tenth_cols = $this->getTable_cols();
        //$db_options is $options with keys renamed a/q to db_columns
        $db_options = array();
        foreach ($options as $key => $value) {
            $db_options[$this->correctDbKeys($key)] = $value;
        }
        $db_options_keys = array_keys($db_options);
        $recieved_tenth_keys = array_intersect($db_options_keys, 
        $all_tenth_cols);
        $tenth_data = array();
        foreach ($recieved_tenth_keys as $key_name) {
            $str = "get" . ucfirst($this->correctModelKeys($key_name));
            $tenth_data[$key_name] = $aisse->$str();
        }
        //Zend_Registry::get('logger')->debug($tenth_data);
        //$adapter = $this->getDbTable()->getAdapter();
        //$where = $adapter->quoteInto("$this->correctDbKeys('member_id') = ?", $aissce->getMember_id());
        $adapter = $this->getDbTable()->getAdapter();
        $table = $this->getDbTable()->info('name');
        $adapter->beginTransaction();
        try {
            $sql = $adapter->insert($table, $tenth_data);
            $adapter->commit();
        } catch (Exception $exception) {
            $adapter->rollBack();
            echo $exception->getMessage() . "</br>";
        }
    }
    /**
     * Enter description here ...
     * @param Acad_Model_Exam_Aisse $aisse
     * @param array $property_range Example :array('name'=>array('from'=>n ,'to'=>m));
     * here 'from' stands for >= AND 'to' stands for <=
     * 
     */
    public function fetchStudents (Acad_Model_Exam_Aisse $aisse, 
    array $setter_options = null, array $property_range = null)
    {
        $correct_db_options = array();
        foreach ($setter_options as $k => $val) {
            $correct_db_options[$this->correctDbKeys($k)] = $val;
        }
        $correct_db_options_keys = array_keys($correct_db_options);
        $correct_db_options1 = array();
        foreach ($property_range as $k1 => $val1) {
            $correct_db_options1[$this->correctDbKeys($k1)] = $val1;
        }
        $correct_db_options1_keys = array_keys($correct_db_options1);
        $merge = array_merge($correct_db_options_keys, 
        $correct_db_options1_keys);
        $table = $this->getDbTable()->info('name');
        //1)get column names of tenth present in arguments received
        $tenth_col = $this->getTable_cols();
        $tenth_intrsctn = array();
        $tenth_intrsctn = array_intersect($tenth_col, $merge);
        $adapter = $this->getDbTable()->getAdapter();
        $select = $adapter->select()->from($table, 'member_id');
        if (count($correct_db_options1)) {
            foreach ($correct_db_options1 as $key => $range) {
                if (! empty($range['from'])) {
                    $select->where("$key >= ?", $range['from']);
                }
                if (! empty($range['to'])) {
                    $select->where("$key <= ?", $range['to']);
                }
            }
        }
        if (count($correct_db_options)) {
            foreach ($correct_db_options as $property_name => $value) {
                $getter_string = 'get' .
                 ucfirst($this->correctModelKeys($property_name));
                $aisse->$getter_string();
                $condition = $property_name . ' = ?';
                $select->where($condition, $value);
            }
        }
        $result = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        if (! count($result)) {
            $search_error = 'No results match your search criteria.';
            return $search_error;
        } else {
            return $result;
        }
    }
    /**
     * Provides correct db column names corresponding to model properties
     * @todo add correct names where required
     * @param string $key
     */
    protected function correctDbKeys ($key)
    {
        switch ($key) {
            /*case 'nationalit':
                return 'nationality';
                break;*/
            default:
                return $key;
                break;
        }
    }
    /**
     * Provides correct model property names corresponding to db column names
     * @todo add correct names where required
     * @param string $key
     */
    protected function correctModelKeys ($key)
    {
        switch ($key) {
            /*case 'nationality':
                return 'nationalit';
                break;*/
            default:
                return $key;
                break;
        }
    }
}