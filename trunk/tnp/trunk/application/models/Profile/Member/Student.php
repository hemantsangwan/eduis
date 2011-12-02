<?php
/**
 * @package TNP
 *
 */
class Tnp_Model_Profile_Member_Student extends Tnp_Model_Generic
{
    protected $_save_student = false;
    protected $_save_skills = false;
    protected $_save_stu_skills = false;
    protected $_save_lang = false;
    protected $_save_stu_lang = false;
    protected $_save_profile_status = false;
    protected $_save_job_pref = false;
    protected $_save_co_curri = false;
    protected $_skills_possessed = array();
    protected $_languages_known = array();
    protected $_member_id;
    protected $_roll_no;
    protected $_department_id;
    protected $_programme_id;
    protected $_semester_id;
    protected $_skill_id;
    protected $_skill_name;
    protected $_skill_field;
    protected $_skill_proficiency;
    protected $_language_id;
    protected $_language_name;
    protected $_language_proficiency;
    protected $_exists;
    protected $_is_locked;
    protected $_last_updated_on;
    protected $_job_preferred;
    protected $_achievements;
    protected $_activities;
    protected $_hobbies;
    protected $_mapper;
    /**
     * @return the $_save_student
     */
    public function getSave_student ()
    {
        return $this->_save_student;
    }
    /**
     * @param field_type $_save_student
     */
    public function setSave_student ($_save_student)
    {
        $this->_save_student = $_save_student;
    }
    /**
     * @return the $_save_skills
     */
    public function getSave_skills ()
    {
        return $this->_save_skills;
    }
    /**
     * @param field_type $_save_skills
     */
    public function setSave_skills ($_save_skills)
    {
        $this->_save_skills = $_save_skills;
    }
    /**
     * @return the $_save_stu_skills
     */
    public function getSave_stu_skills ()
    {
        return $this->_save_stu_skills;
    }
    /**
     * @param field_type $_save_stu_skills
     */
    public function setSave_stu_skills ($_save_stu_skills)
    {
        $this->_save_stu_skills = $_save_stu_skills;
    }
    /**
     * @return the $_save_lang
     */
    public function getSave_lang ()
    {
        return $this->_save_lang;
    }
    /**
     * @param field_type $_save_lang
     */
    public function setSave_lang ($_save_lang)
    {
        $this->_save_lang = $_save_lang;
    }
    /**
     * @return the $_save_stu_lang
     */
    public function getSave_stu_lang ()
    {
        return $this->_save_stu_lang;
    }
    /**
     * @param field_type $_save_stu_lang
     */
    public function setSave_stu_lang ($_save_stu_lang)
    {
        $this->_save_stu_lang = $_save_stu_lang;
    }
    /**
     * @return the $_save_profile_status
     */
    public function getSave_profile_status ()
    {
        return $this->_save_profile_status;
    }
    /**
     * @param field_type $_save_profile_status
     */
    public function setSave_profile_status ($_save_profile_status)
    {
        $this->_save_profile_status = $_save_profile_status;
    }
    /**
     * @return the $_save_job_pref
     */
    public function getSave_job_pref ()
    {
        return $this->_save_job_pref;
    }
    /**
     * @param field_type $_save_job_pref
     */
    public function setSave_job_pref ($_save_job_pref)
    {
        $this->_save_job_pref = $_save_job_pref;
    }
    /**
     * @return the $_save_co_curri
     */
    public function getSave_co_curri ()
    {
        return $this->_save_co_curri;
    }
    /**
     * @param field_type $_save_co_curri
     */
    public function setSave_co_curri ($_save_co_curri)
    {
        $this->_save_co_curri = $_save_co_curri;
    }
    protected function getSkills_possessed ()
    {
        $skills_possessed = $this->getMapper()->fetchSkillsPossessedInfo($this);
        $this->setSkills_possessed($skills_possessed);
        return $this->_skills_possessed;
    }
    protected function setSkills_possessed ($_skills_possessed)
    {
        $this->_skills_possessed = $_skills_possessed;
    }
    protected function getLanguages_known ()
    {
        $languages_known = $this->getMapper()->fetchLanguagesKnownInfo($this);
        $this->setLanguages_known($languages_known);
        return $this->_languages_known;
    }
    protected function setLanguages_known ($_languages_known)
    {
        $this->_languages_known = $_languages_known;
    }
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
     * @return the $_department_id
     */
    public function getDepartment_id ()
    {
        return $this->_department_id;
    }
    /**
     * @param field_type $_department_id
     */
    public function setDepartment_id ($_department_id)
    {
        $this->_department_id = $_department_id;
    }
    /**
     * @return the $_programme_id
     */
    public function getProgramme_id ()
    {
        return $this->_programme_id;
    }
    /**
     * @param field_type $_programme_id
     */
    public function setProgramme_id ($_programme_id)
    {
        $this->_programme_id = $_programme_id;
    }
    /**
     * @return the $_semester_id
     */
    public function getSemester_id ()
    {
        return $this->_semester_id;
    }
    /**
     * @param field_type $_semester_id
     */
    public function setSemester_id ($_semester_id)
    {
        $this->_semester_id = $_semester_id;
    }
    /**
     * @return the $_skill_id
     */
    public function getSkill_id ()
    {
        return $this->_skill_id;
    }
    /**
     * @param field_type $_skill_id
     */
    public function setSkill_id ($_skill_id)
    {
        $this->_skill_id = $_skill_id;
    }
    /**
     * @return the $_skill_name
     */
    public function getSkill_name ()
    {
        return $this->_skill_name;
    }
    /**
     * @param field_type $_skill_name
     */
    public function setSkill_name ($_skill_name)
    {
        $this->_skill_name = $_skill_name;
    }
    /**
     * @return the $_skill_field
     */
    public function getSkill_field ()
    {
        return $this->_skill_field;
    }
    /**
     * @param field_type $_skill_field
     */
    public function setSkill_field ($_skill_field)
    {
        $this->_skill_field = $_skill_field;
    }
    /**
     * @return the $_skill_proficiency
     */
    public function getSkill_proficiency ()
    {
        return $this->_skill_proficiency;
    }
    /**
     * @param field_type $_skill_proficiency
     */
    public function setSkill_proficiency ($_skill_proficiency)
    {
        $this->_skill_proficiency = $_skill_proficiency;
    }
    /**
     * @return the $_language_id
     */
    public function getLanguage_id ()
    {
        return $this->_language_id;
    }
    /**
     * @param field_type $_language_id
     */
    public function setLanguage_id ($_language_id)
    {
        $this->_language_id = $_language_id;
    }
    /**
     * @return the $_language_name
     */
    public function getLanguage_name ()
    {
        return $this->_language_name;
    }
    /**
     * @param field_type $_language_name
     */
    public function setLanguage_name ($_language_name)
    {
        $this->_language_name = $_language_name;
    }
    /**
     * @return the $_language_proficiency
     */
    public function getLanguage_proficiency ()
    {
        return $this->_language_proficiency;
    }
    /**
     * @param field_type $_language_proficiency
     */
    public function setLanguage_proficiency ($_language_proficiency)
    {
        $this->_language_proficiency = $_language_proficiency;
    }
    /**
     * @return the $_exists
     */
    public function getExists ()
    {
        return $this->_exists;
    }
    /**
     * @param field_type $_exists
     */
    public function setExists ($_exists)
    {
        $this->_exists = $_exists;
    }
    /**
     * @return the $_is_locked
     */
    public function getIs_locked ()
    {
        return $this->_is_locked;
    }
    /**
     * @param field_type $_is_locked
     */
    public function setIs_locked ($_is_locked)
    {
        $this->_is_locked = $_is_locked;
    }
    /**
     * @return the $_last_updated_on
     */
    public function getLast_updated_on ()
    {
        return $this->_last_updated_on;
    }
    /**
     * @param field_type $_last_updated_on
     */
    public function setLast_updated_on ($_last_updated_on)
    {
        $this->_last_updated_on = $_last_updated_on;
    }
    /**
     * @return the $_job_preferred
     */
    public function getJob_preferred ()
    {
        return $this->_job_preferred;
    }
    /**
     * @param field_type $_job_preferred
     */
    public function setJob_preferred ($_job_preferred)
    {
        $this->_job_preferred = $_job_preferred;
    }
    /**
     * @return the $_achievements
     */
    public function getAchievements ()
    {
        return $this->_achievements;
    }
    /**
     * @param field_type $_achievements
     */
    public function setAchievements ($_achievements)
    {
        $this->_achievements = $_achievements;
    }
    /**
     * @return the $_activities
     */
    public function getActivities ()
    {
        return $this->_activities;
    }
    /**
     * @param field_type $_activities
     */
    public function setActivities ($_activities)
    {
        $this->_activities = $_activities;
    }
    /**
     * @return the $_hobbies
     */
    public function getHobbies ()
    {
        return $this->_hobbies;
    }
    /**
     * @param field_type $_hobbies
     */
    public function setHobbies ($_hobbies)
    {
        $this->_hobbies = $_hobbies;
    }
    /**
     * Set Mapper
     * @param Tnp_Model_Mapper_Member_Student $mapper
     * @return Tnp_Model_Profile_Member_Student
     */
    public function setMapper ($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * gets the mapper from the object class
     * @return Tnp_Model_Mapper_Profile_Member_Student
     */
    public function getMapper ()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Tnp_Model_Mapper_Profile_Member_Student());
        }
        return $this->_mapper;
    }
    public function initProfileStatusInfo ()
    {
        $options = $this->getMapper()->fetchProfileStatusInfo($this);
        $this->setOptions($options);
    }
    public function getMemberSkillIds ()
    {
        $possessed_skills = $this->getSkills_possessed();
        $possessed_skills_ids = array_keys($possessed_skills);
        if (sizeof($possessed_skills_ids) == 0) {
            $error = 'No skills registered for ' . $this->getMember_id();
            throw new Exception($error);
        } else {
            return $possessed_skills_ids;
        }
    }
    public function getMemberLanguageKnownIds ()
    {
        $languages_known = $this->getLanguages_known();
        $language_ids = array_keys($languages_known);
        if (sizeof($language_ids) == 0) {
            $error = 'languages known are not registered for ' .
             $this->getMember_id();
            throw new Exception($error);
        } else {
            return $language_ids;
        }
    }
    public function initSkillInfo ()
    {
        $options = $this->getMapper()->fetchSkillInfo($this);
        $this->setOptions($options);
    }
    public function initLanguageInfo ()
    {
        $options = $this->getMapper()->fetchLanguageInfo($this);
        $this->setOptions($options);
    }
    public function initMemberSkillInfo ()
    {
        $skills_possessed = $this->getSkills_possessed();
        $skill_id = $this->getSkill_id();
        if (array_key_exists($skill_id, $skills_possessed)) {
            $options = $skills_possessed[$skill_id];
            $this->setOptions($options);
        } else {
            $error = 'No skill entries exist for Skill Id ' . $skill_id;
            throw new Exception($error);
        }
    }
    public function initMemberLanguageInfo ()
    {
        $languages_known = $this->getLanguages_known();
        $language_id = $this->getLanguage_id();
        if (array_key_exists($language_id, $languages_known)) {
            $options = $languages_known[$language_id];
            $this->setOptions($options);
        } else {
            $error = 'No language entries exist for Language Id ' . $language_id;
            throw new Exception($error);
        }
    }
    public function initCoCuricularInfo ()
    {
        $options = $this->getMapper()->fetchCoCuricularInfo($this);
        $this->setOptions($options);
    }
    public function initJobPreferredInfo ()
    {
        $options = $this->getMapper()->fetchJobPreferredInfo($this);
        $this->setOptions($options);
    }
    /**
     * @todo reg no included in search
     * Enter description here ...
     * @throws Exception
     */
    public function findMemberID ()
    {
        $roll_no = $this->getRoll_no();
        $department_id = $this->getDepartment_id();
        $programme_id = $this->getProgramme_id();
        $semester_id = $this->getSemester_id();
        if (! isset($roll_no) or ! isset($department_id) or
         ! isset($programme_id) or ! isset($semester_id)) {
            throw new Exception(
            'Insufficient data provided..   roll_no,department_id,programme_id and semester_id are ALL required');
        } else {
            $options = $this->getMapper()->fetchMemberID($this);
            $this->setOptions($options);
        }
    }
    public function findRollNo ()
    {
        $member_id = $this->getMember_id();
        $department_id = $this->getDepartment_id();
        $programme_id = $this->getProgramme_id();
        $semester_id = $this->getSemester_id();
        if (! isset($member_id)) {
            throw new Exception(
            'Insufficient data provided..   department_id,programme_id and semester_id are ALL required');
        } else {
            $options = $this->getMapper()->fetchRollNo($this);
            $this->setOptions($options);
        }
    }
    public function enroll ($options)
    {
        $roll_no = $options['roll_no'];
        if (! isset($roll_no)) {
            throw new Exception(
            'Insufficient data provided..   roll_no is required');
        } else {
            $this->setSave_student(true);
            parent::save($options);
        }
    }
}