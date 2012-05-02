<?php
class Core_Model_StudentClass extends Core_Model_Generic
{
    protected $_member_id;
    protected $_class_id;
    protected $_group_id;
    protected $_roll_no;
    protected $_start_date;
    protected $_completion_date;
    protected $_is_initial_batch_identifier;
    protected $_mapper;
    /**
     * @return the $_member_id
     */
    public function getMember_id ()
    {
        return $this->_member_id;
    }
    /**
     * @param field_type $_member_id
     */
    public function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @return the $_class_id
     */
    public function getClass_id ()
    {
        return $this->_class_id;
    }
    /**
     * @param field_type $_class_id
     */
    public function setClass_id ($_class_id)
    {
        $this->_class_id = $_class_id;
    }
    /**
     * @return the $_group_id
     */
    public function getGroup_id ()
    {
        return $this->_group_id;
    }
    /**
     * @param field_type $_group_id
     */
    public function setGroup_id ($_group_id)
    {
        $this->_group_id = $_group_id;
    }
    /**
     * @return the $_roll_no
     */
    public function getRoll_no ()
    {
        return $this->_roll_no;
    }
    /**
     * @param field_type $_roll_no
     */
    public function setRoll_no ($_roll_no)
    {
        $this->_roll_no = $_roll_no;
    }
    /**
     * @return the $_start_date
     */
    public function getStart_date ()
    {
        return $this->_start_date;
    }
    /**
     * @param field_type $_start_date
     */
    public function setStart_date ($_start_date)
    {
        $this->_start_date = $_start_date;
    }
    /**
     * @return the $_completion_date
     */
    public function getCompletion_date ()
    {
        return $this->_completion_date;
    }
    /**
     * @param field_type $_completion_date
     */
    public function setCompletion_date ($_completion_date)
    {
        $this->_completion_date = $_completion_date;
    }
    /**
     * @return the $_is_initial_batch_identifier
     */
    public function getIs_initial_batch_identifier ()
    {
        return $this->_is_initial_batch_identifier;
    }
    /**
     * @param field_type $_is_initial_batch_identifier
     */
    public function setIs_initial_batch_identifier (
    $_is_initial_batch_identifier)
    {
        $this->_is_initial_batch_identifier = $_is_initial_batch_identifier;
    }
    /**
     * Sets Mapper
     * @param Core_Model_Mapper_StudentClass $mapper
     * @return Core_Model_StudentClass
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * gets the mapper from the object class
     * @return Core_Model_Mapper_StudentClass
     */
    public function getMapper ()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Core_Model_Mapper_StudentClass());
        }
        return $this->_mapper;
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
    /**
     * 
     */
    public function initInfo ()
    {}
    /**
     * Fetches information regarding class
     *
     */
    public function fetchInfo ()
    {
        $member_id = $this->getMember_id();
        $class_id = $this->getClass_id();
        if (empty($member_id) or empty($class_id)) {
            $error = 'Please provide a Member Id and a Class id';
            throw new Exception($error, Zend_Log::ERR);
        } else {
            $info = $this->getMapper()->fetchInfo($member_id, $class_id);
            if (sizeof($info) == 0) {
                return false;
            } else {
                $this->setOptions($info);
                return true;
            }
        }
    }
    public function fetchBatchIdentifierClassId ()
    {
        $member_id = $this->getMember_id();
        if (empty($member_id)) {
            $error = 'Insufficient Params supplied to fetchBatchIdentifierClassId .Member_id required';
            throw new Exception($error, Zend_Log::ERR);
        } else {
            return $this->getMapper()->fetchBatchIdentifierClassId($member_id);
        }
    }
    public function fetchClassIds ()
    {
        $member_id = $this->getMember_id();
        if (empty($member_id)) {
            $error = 'Please provide a Member Id';
            throw new Exception($error, Zend_Log::ERR);
        } else {
            $class_ids = $this->getMapper()->fetchClassIds($member_id);
            if (sizeof($class_ids) == 0) {
                return false;
            } else {
                return $class_ids;
            }
        }
    }
    public function save ($data_array)
    {
        $preparedDataForSaveProcess = $this->prepareDataForSaveProcess(
        $data_array);
        $this->setOptions($preparedDataForSaveProcess);
        $this->getMapper()->save($preparedDataForSaveProcess);
    }
}