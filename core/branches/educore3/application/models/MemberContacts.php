<?php
class Core_Model_MemberContacts extends Core_Model_Generic
{
    protected $_member_id;
    protected $_contact_type_id;
    protected $_contact_type_name;
    protected $_contact_details;
    protected $_mapper;
    /**
     * @param bool $throw_exception optional
     * @return the $_member_id
     */
    public function getMember_id ($throw_exception = null)
    {
        $member_id = $this->_member_id;
        if (empty($member_id) and $throw_exception == true) {
            $message = 'Member_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $member_id;
        }
    }
    /**
     * @return the $_contact_type_id
     */
    public function getContact_type_id ($throw_exception = null)
    {
        $contact_type_id = $this->_contact_type_id;
        if (empty($contact_type_id) and $throw_exception == true) {
            $message = '_contact_type_id is not set in ' . get_class($this);
            $code = Zend_Log::ERR;
            throw new Exception($message, $code);
        } else {
            return $contact_type_id;
        }
    }
    /**
     * @return the $_contact_type_name
     */
    public function getContact_type_name ()
    {
        return $this->_contact_type_name;
    }
    /**
     * @return the $_contact_details
     */
    public function getContact_details ()
    {
        return $this->_contact_details;
    }
    /**
     * @param field_type $_member_id
     */
    public function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @param field_type $_contact_type_id
     */
    public function setContact_type_id ($_contact_type_id)
    {
        $this->_contact_type_id = $_contact_type_id;
    }
    /**
     * @param field_type $_contact_type_name
     */
    public function setContact_type_name ($_contact_type_name)
    {
        $this->_contact_type_name = $_contact_type_name;
    }
    /**
     * @param field_type $_contact_details
     */
    public function setContact_details ($_contact_details)
    {
        $this->_contact_details = $_contact_details;
    }
    /**
     * Sets Mapper
     * @param Core_Model_Mapper_MemberContacts $mapper
     * @return Core_Model_MemberContacts
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * gets the mapper from the object class
     * @return Core_Model_Mapper_MemberContacts
     */
    public function getMapper ()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Core_Model_Mapper_MemberContacts());
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
        $member_id = $this->getMember_id(true);
        $contact_type_id = $this->getContact_type_id(true);
        $info = $this->getMapper()->fetchInfo($member_id, $contact_type_id);
        if (empty($info)) {
            return false;
        } else {
            return $this->setOptions($info);
        }
    }
    /**
     * Fetches all contact Types of a member
     *
     */
    public function fetchContactTypeIds ()
    {
        $member_id = $this->getMember_id(true);
        $contact_type_ids = $this->getMapper()->fetchContactTypeIds($member_id);
        if (empty($contact_type_ids)) {
            return false;
        } else {
            return $contact_type_ids;
        }
    }
    public function fetchAllContactTypes ()
    {
        $contacty_types = $this->getMapper()->fetchAllContactTypes();
        if (empty($contacty_types)) {
            return false;
        } else {
            return $contacty_types;
        }
    }
}
