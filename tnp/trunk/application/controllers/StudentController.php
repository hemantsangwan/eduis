<?php
class StudentController extends Zend_Controller_Action
{
    public function aclconfigAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $methods = get_class_methods('StudentController');
        $actions = array();
        foreach ($methods as $value) {
            $actions[] = substr("$value", 0, strpos($value, 'Action'));
        }
        foreach ($actions as $key => $value) {
            if ($value == null) {
                unset($actions[$key]);
            }
        }
        $db = new Zend_Db_Table();
        $delete2 = 'DELETE FROM `tnp`.`mod_role_resource` WHERE `module_id`=? AND `controller_id`=?';
        $db->getAdapter()->query($delete2, array('tnp', 'student'));
        $delete1 = 'DELETE FROM `tnp`.`mod_action` WHERE `module_id`=? AND `controller_id`=?';
        $db->getAdapter()->query($delete1, array('tnp', 'student'));
        print_r(sizeof($actions));
        $sql = 'INSERT INTO `tnp`.`mod_action`(`module_id`,`controller_id`,`action_id`) VALUES (?,?,?)';
        foreach ($actions as $action) {
            $bind = array('tnp', 'student', $action);
            $db->getAdapter()->query($sql, $bind);
        }
        $sql = 'INSERT INTO `tnp`.`mod_role_resource`(`role_id`,`module_id`,`controller_id`,`action_id`) VALUES (?,?,?,?)';
        foreach ($actions as $action) {
            $bind = array('student', 'tnp', 'student', $action);
            $db->getAdapter()->query($sql, $bind);
        }
    }
    /**
     * 
     * @var int
     */
    protected $_member_id;
    protected $_user_name;
    protected $_user_type;
    protected $_department_id;
    /**
     * @return the $_member_id
     */
    protected function getMember_id ()
    {
        return $this->_member_id;
    }
    /**
     * @return the $_user_name
     */
    protected function getUser_name ()
    {
        return $this->_user_name;
    }
    /**
     * @return the $_user_type
     */
    protected function getUser_type ()
    {
        return $this->_user_type;
    }
    /**
     * @return the $_department_id
     */
    protected function getDepartment_id ()
    {
        return $this->_department_id;
    }
    /**
     * @param int $_member_id
     */
    protected function setMember_id ($_member_id)
    {
        $this->_member_id = $_member_id;
    }
    /**
     * @param field_type $_user_name
     */
    protected function setUser_name ($_user_name)
    {
        $this->_user_name = $_user_name;
    }
    /**
     * @param field_type $_user_type
     */
    protected function setUser_type ($_user_type)
    {
        $this->_user_type = $_user_type;
    }
    /**
     * @param field_type $_department_id
     */
    protected function setDepartment_id ($_department_id)
    {
        $this->_department_id = $_department_id;
    }
    public function indexAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
    }
    public function init ()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $authInfo = Zend_Auth::getInstance()->getStorage()->read();
            $this->setDepartment_id($authInfo['department_id']);
            $this->setUser_name($authInfo['identity']);
            $this->setUser_type($authInfo['userType']);
            $this->setMember_id($authInfo['member_id']);
        }
    }
    public function memberidcheckAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        if (! empty($member_id)) {
            $member_id_exists = $this->memberIdCheck($member_id);
            $format = $this->_getParam('format', 'log');
            switch ($format) {
                case 'html':
                    $this->_helper->viewRenderer->setNoRender(false);
                    $this->_helper->layout()->enableLayout();
                    $this->view->assign('member_id_exists', $member_id_exists);
                    break;
                case 'jsonp':
                    $callback = $this->getRequest()->getParam('callback');
                    echo $callback . '(' . $this->_helper->json(
                    $member_id_exists, false) . ')';
                    break;
                case 'json':
                    $this->_helper->json($member_id_exists);
                    break;
                case 'log':
                    //Zend_Registryget('logger')->debug($member_id_exists);
                    break;
                default:
                    ;
                    break;
            }
        }
    }
    public function saveclassinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $class_info = $params['class_info'];
        $class_ids = $class_info['class_ids'];
        $member_id = $class_info['member_id'];
        $student_class_info['member_id'] = $class_info['member_id'];
        $student_class_info['roll_no'] = $class_info['roll_no'];
        $student_class_info['group_id'] = $class_info['group_id'];
        foreach ($class_ids as $class_id) {
            $student_class_info['class_id'] = $class_id;
            $this->saveClassInfo($member_id, $student_class_info);
        }
        $format = $this->_getParam('format', 'log');
        switch ($format) {
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' . $this->_helper->json(true, false) . ')';
                break;
            case 'json':
                $this->_helper->json(true);
                break;
            case 'log':
                //Zend_Registryget('logger')->debug(true);
                break;
            default:
                ;
                break;
        }
    }
    public function exportexcelAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $core_data = $params['myarray']['core_data'];
        $academic_data = $params['myarray']['academic_data'];
        $final_data = array();
        foreach ($core_data as $member_id_core => $info) {
            if (! empty($academic_data[$member_id_core])) {
                $member_data = array_merge($core_data[$member_id_core], 
                $academic_data[$member_id_core]);
                $final_data[$member_id_core] = $member_data;
            }
        }
        $exportable_data = $final_data;
        $headings = array_pop($final_data);
        $column_headers = array_keys($headings);
        $file_id = time();
        $this->exportToExcel($column_headers, $exportable_data, $file_id);
        $format = $this->_getParam('format', 'log');
        switch ($format) {
            case 'html':
                $this->_helper->viewRenderer->setNoRender(false);
                $this->_helper->layout()->enableLayout();
                $this->view->assign('data', $file_id);
                break;
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' . $this->_helper->json($file_id, false) .
                 ')';
                break;
            case 'json':
                $this->_helper->json($file_id);
                break;
            case 'log':
                //Zend_Registryget('logger')->debug($file_id);
                break;
            default:
                ;
                break;
        }
    }
    public function saveexcelonclientAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $file_id = $params['file_id'];
        $org_file = DATA_EXCEL . "/Student_Data-" . $file_id . ".xlsx";
        $this->getResponse()
            ->setRawHeader(
        "Content-Type: application/vnd.ms-excel; charset=UTF-8")
            ->setRawHeader(
        "Content-Disposition: attachment; filename=Student_Data.xlsx")
            ->setRawHeader("Content-Transfer-Encoding: binary")
            ->setRawHeader("Expires: 0")
            ->setRawHeader(
        "Cache-Control: must-revalidate, post-check=0, pre-check=0")
            ->setRawHeader("Pragma: public")
            ->setRawHeader("Content-Length: " . filesize($org_file))
            ->sendResponse();
        $response = $this->_response;
        ob_end_clean();
        $bits = @file_get_contents($org_file);
        $response->setBody($bits);
    /**
     * Old Code
     * @deprecated
     */
    /*$temp_file = DATA_EXCEL . "/temp" . $file_id . ".xlsx";
        $realPath = realpath($temp_file);
        if ($realPath == false) {
            touch($temp_file);
            chmod($temp_file, 0777);
        }
        $handle = fopen($temp_file, "w");
        $org_file = DATA_EXCEL . "/Student_Data-" . $file_id . ".xlsx";
        $contents = @file_get_contents($org_file);
        fputs($handle, $contents);
        fclose($handle);
        $this->getResponse()
            ->setRawHeader(
        "Content-Type: application/vnd.ms-excel; charset=UTF-8")
            ->setRawHeader(
        "Content-Disposition: attachment; filename=Student_Data.xlsx")
            ->setRawHeader("Content-Transfer-Encoding: binary")
            ->setRawHeader("Expires: 0")
            ->setRawHeader(
        "Cache-Control: must-revalidate, post-check=0, pre-check=0")
            ->setRawHeader("Pragma: public")
            ->setRawHeader("Content-Length: " . filesize($temp_file))
            ->sendResponse();
        readfile(realpath($temp_file));*/
    }
    public function fetchcriticalinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $critical_data = self::findCriticalInfo($member_id);
        $this->_helper->json($critical_data);
    }
    public function viewemptestAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
    }
    public function viewemptestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $test_record = $this->generateEmpTestRecords($member_id);
        Zend_Registry::get('logger')->debug(
        'Vars assigned to view are : \'test_record\' where the key is the test_record_id');
        Zend_Registry::get('logger')->debug($test_record);
        $this->view->assign('test_record', $test_record);
    }
    public function viewskillinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $skill_info = $this->findSkillsInfo($member_id);
        $this->view->assign('skill_info', $skill_info);
         //Zend_Registryget('logger')->debug($skill_info);
    }
    public function viewlanguagesknownAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        Zend_Registry::get('logger')->debug($member_id);
        $language_info = $this->findLanguageInfo($member_id);
        $this->view->assign('language_info', $language_info);
    }
    public function viewcocurricularAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $co_curicular_info = $this->findCocurricularInfo($member_id);
        Zend_Registry::get('logger')->debug($co_curicular_info);
        $this->view->assign('co_curicular_info', $co_curicular_info);
    }
    public function viewjobpreferredAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $job_preferred = $this->findJobPreferred($member_id);
        //Zend_Registryget('logger')->debug($job_preferred);
        $this->view->assign('job_preferred', $job_preferred);
    }
    public function viewtraininginfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $training_info = $this->generateTrainingInfo($member_id);
        $this->view->assign('training_info', $training_info);
         //Zend_Registryget('logger')->debug($training_info);
    }
    public function viewcertificationinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $certification_info = $this->generateCertificationInfo($member_id);
        $this->view->assign('certification_info', $certification_info);
         //Zend_Registryget('logger')->debug($certification_info);
    }
    /**
     * assigns test and section record for a given employability_test_id of member_id
     * Enter description here ...
     */
    public function editemptestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $format = $this->_getParam('format', 'html');
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $test_record_id = $params['test_record_id'];
        $test_record = $this->getEmpTestRecordInfo($test_record_id, $member_id);
        $employability_test_id = $test_record['employability_test_id'];
        $test_info = $this->getEmpTestInfo($employability_test_id);
        $test_record['test_name'] = $test_info['test_name'];
        $test_record['date_of_conduct'] = $test_info['date_of_conduct'];
        $section_record = $this->generateSectionScore($employability_test_id, 
        $member_id);
        //Zend_Registryget('logger')->debug($test_record);
        //Zend_Registryget('logger')->debug($section_record);
        switch ($format) {
            case 'html':
                $this->view->assign('test_record', $test_record);
                $this->view->assign('section_record', $section_record);
                break;
            default:
                ;
                break;
        }
    }
    /**
     * Enables the user add edit existing skills or add new skills to database
     * 
     */
    public function editskillsAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $skill_id = $params['skill_id'];
        $info = $this->findSkillsInfo($member_id);
        $skill_info = array();
        $skill_info['skill_id'] = $skill_id;
        $skill_info['skill_name'] = $info[$skill_id]['skill_name'];
        $skill_info['proficiency'] = $info[$skill_id]['proficiency'];
        //Zend_Registryget('logger')->debug($skill_info);
        $this->view->assign('skill_info', $skill_info);
    }
    public function editlanguageknownAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $language_id = $params['language_id'];
        $info = $this->findLanguageInfo($member_id);
        $language_info = array();
        $language_info['language_id'] = $language_id;
        $language_info['language_name'] = $info[$language_id]['language_name'];
        $language_info['proficiency'] = $info[$language_id]['proficiency'];
        //Zend_Registryget('logger')->debug($language_info);
        $this->view->assign('language_info', $language_info);
    }
    public function editcocurricularAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $cocurricular_info = $this->findCocurricularInfo($member_id);
        //Zend_Registryget('logger')->debug($cocurricular_info);
        $this->view->assign('cocurricular_info', $cocurricular_info);
    }
    public function editjobpreferredAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $job_preferred = $this->findJobPreferred($member_id);
        //Zend_Registryget('logger')->debug($job_preferred);
        $this->view->assign('job_preferred', $job_preferred);
    }
    public function edittraininginfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $training_id = $params['training_id'];
        $functional_areas = $this->fetchFunctionalAreas();
        $training_info = $this->findTrainingInfo($member_id, $training_id);
        $training_info['training_id'] = $training_id;
        $this->view->assign('functional_areas', $functional_areas);
        $this->view->assign('training_info', $training_info);
         //Zend_Registryget('logger')->debug($functional_areas);
    //Zend_Registryget('logger')->debug($training_info);
    }
    public function editcertificationAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $functional_areas = $this->fetchFunctionalAreas();
        $this->view->assign('functional_areas', $functional_areas);
        $certification_id = $params['certification_id'];
        $certification_info = $this->findCertificationsInfo($member_id, 
        $certification_id);
        $certification_info['certification_id'] = $certification_id;
        //Zend_Registryget('logger')->debug($certification_info);
        $this->view->assign('certification_info', $certification_info);
    }
    public function deletetestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $test_record_id = $params['test_record_id'];
        $this->deleteEmpTestRecord($test_record_id);
    }
    public function deleteskillinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $skill_id = $params['skill_id'];
        $this->deleteSkill($member_id, $skill_id);
    }
    public function deletelanguageAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $language_id = $params['language_id'];
        $this->deleteLanguageKnown($member_id, $language_id);
    }
    public function deletecocurricularAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $this->deleteCoCurricular($member_id);
    }
    public function deletejobpreferredAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        return $this->deletejobpreferred($member_id);
    }
    public function deletetrainingAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $training_id = $params['training_id'];
        $this->deletetraining($member_id, $training_id);
    }
    public function deletestucertificationAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $certification_id = $params['certification_id'];
        $this->deleteStuCertification($member_id, $certification_id);
    }
    /**
     * for testing purposes only
     * Enter description here ...
     */
    public function profileAction ()
    {
        $response = array();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $format = $this->_getParam('format', 'html');
        $response['employability_test'] = $this->findEmpTestInfo();
        /*------------------------------------------------------------------------------*/
        $response['certifications'] = $this->findCertificationsInfo();
        /*------------------------------------------------------------------------------*/
        $response['training'] = $this->findTrainingInfo();
        /*------------------------------------------------------------------------------*/
        $response['experience'] = $this->findExperienceInfo($member_id);
        /*------------------------------------------------------------------------------*/
        $response['language'] = $this->findLanguageInfo();
        /*------------------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------*/
        //Zend_Registryget('logger')->debug($response);
        switch ($format) {
            case 'html':
                $this->view->assign('response', $response);
                break;
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' . $this->_helper->json($response, false) .
                 ')';
                break;
            case 'json':
                $this->_helper->json($response);
                break;
            case 'test':
                //Zend_Registryget('logger')->debug($response);
                break;
            default:
                ;
                break;
        }
    }
    public function addmemberskillAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $skills = $this->fetchSkills();
        $this->view->assign('skills', $skills);
    }
    public function addmemberlanguageAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $languages = $this->fetchLanguages();
        /*//Zend_Registryget('logger')->debug($languages);*/
        $this->view->assign('languages', $languages);
    }
    public function addcocurricularAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $co_curicular_info = $this->findCocurricularInfo($member_id);
        //Zend_Registryget('logger')->debug($co_curicular_info);
        $this->view->assign('co_curicular_info', $co_curicular_info);
    }
    public function addskillAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $skill_info = $params['skill_info'];
        $this->addSkill($skill_info);
    }
    /**
     * Add/Defines new test in database
     * Enter description here ...
     */
    public function addemptestAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        //Zend_Registryget('logger')->debug(
        //'params required are \'test_info\' myarray[\'test_info\']');
        $test_info = $params['myarray']['test_info'];
        $this->addEmpTest($test_info);
    }
    public function addlanguageAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        if (empty($params['myarray'])) {
            //Zend_Registryget('logger')->debug(
        //'params required are  : myarray[\'language_info\'] where \'language_info\' is an array containing \'language name\'');
        //Zend_Registryget('logger')->debug('\'myarray\' was EMPTY');
        } else {
            $language_info = $params['myarray']['language_info'];
            $this->addLanguage($language_info);
        }
    }
    public function addjobpreferredAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $job_areas = array('CORE', 'DEFENCE', 'GOVERNMENT', 'IT');
        $this->view->assign('job_areas', $job_areas);
    }
    /**
     * add new sections to a test
     * Enter description here ...
     */
    public function addemptestsectionAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        //Zend_Registryget('logger')->debug(
        //'params requires are \'section_info\' myarray[\'section_info\']');
        $test_info = $params['myarray']['test_info'];
        $employability_test_id = $this->addEmpTest($test_info);
        $test_section_name = $params['myarray']['section_info']['test_section_name'];
        $info['employability_test_id'] = $employability_test_id;
        $info['test_section_name'] = $test_section_name;
        $test_section_id = $this->addEmpTestSection($info);
        $response = array('test_section_id' => $test_section_id, 
        'test_section_name' => $test_section_name);
        $this->_helper->json($response);
    }
    /**
     * add new sections to a test
     * Enter description here ...
     */
    public function addemptestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $test_model = new Tnp_Model_EmpTestInfo_Test();
        $test_names = $test_model->fetchTests();
        $this->view->assign('test_names', $test_names);
    }
    public function fetchemptestsectionsAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $test_info = $params['myarray']['test_info'];
        $employability_test_id = $this->addEmpTest($test_info);
        $test_section = new Tnp_Model_EmpTestInfo_Section();
        $test_section->setEmployability_test_id($employability_test_id);
        $test_sections = $test_section->fetchTestSections();
        $this->_helper->json($test_sections);
    }
    public function addtraininginfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $functional_areas = $this->fetchFunctionalAreas();
        /*//Zend_Registryget('logger')->debug($functional_areas);*/
        $this->view->assign('functional_areas', $functional_areas);
    }
    public function addcertificationinfoAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $functional_areas = $this->fetchFunctionalAreas();
        $certifications = $this->fetchCertifications();
        /*//Zend_Registryget('logger')->debug($certifications);
        //Zend_Registryget('logger')->debug($functional_areas);*/
        $this->view->assign('functional_areas', $functional_areas);
        $this->view->assign('certifications', $certifications);
    }
    public function fetchemptestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->layout()->enableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $format = $this->_getParam('format', 'html');
        $employability_test_id = $params['employability_test_id'];
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $test_record_id = $this->getEmpTestRecordId($member_id, 
        $employability_test_id);
        $test_record = $this->getEmpTestRecordInfo(array_pop($test_record_id), 
        $member_id);
        switch ($format) {
            case 'html':
                $this->view->assign('test_record', $test_record);
                break;
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' . $this->_helper->json($test_record, false) .
                 ')';
                break;
            case 'json':
                $this->_helper->json($test_record);
                break;
            case 'test':
                //Zend_Registryget('logger')->debug($test_record);
                break;
            default:
                ;
                break;
        }
    }
    public function fetchsectionrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $format = $this->_getParam('format', 'html');
        $params = array_diff($request->getParams(), $request->getUserParams());
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $employability_test_id = $params['employability_test_id'];
        $section_record = $this->generateSectionScore($employability_test_id, 
        $member_id);
        switch ($format) {
            case 'html':
                $this->view->assign('section_record', $section_record);
                break;
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' .
                 $this->_helper->json($section_record, false) . ')';
                break;
            case 'json':
                $this->_helper->json($section_record);
                break;
            case 'test':
                //Zend_Registryget('logger')->debug($section_record);
                break;
            default:
                ;
                break;
        }
    }
    public function fetchemptestidAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $format = $this->_getParam('format', 'html');
        //Zend_Registryget('logger')->debug(
        //'params required are \'test_info\' myarray[\'test_info\']');
        $test_info = $params['myarray']['test_info'];
        $employability_test_id = $this->addEmpTest($test_info);
        switch ($format) {
            case 'html':
                $this->_helper->viewRenderer->setNoRender(false);
                $this->_helper->layout()->enableLayout();
                $this->view->assign('employability_test_id', 
                $employability_test_id);
                break;
            case 'jsonp':
                $callback = $this->getRequest()->getParam('callback');
                echo $callback . '(' .
                 $this->_helper->json($employability_test_id, false) . ')';
                break;
            case 'json':
                $this->_helper->json($employability_test_id);
                break;
            case 'test':
                //Zend_Registryget('logger')->debug($employability_test_id);
                break;
            default:
                ;
                break;
        }
    }
    /**
     * Student Skill save functionality is provided by this fucntion
     * 
     */
    public function saveskillsAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $is_new_skill = $params['myarray']['new_skill'];
        $skill_info = $params['myarray']['skill_info'];
        $member_proficiency = $skill_info['proficiency'];
        /*
         * skill id sent by user
         */
        if (! empty($skill_info['skill_id'])) {
            $skill_id = $skill_info['skill_id'];
        }
        /*
         * if skill does not exist in databse add it, otherwise update member's proficiency
         */
        if ($is_new_skill == 'true') {
            $skill = new Tnp_Model_Skill();
            $skill_data = array('skill_name' => $skill_info['skill_name']);
            $skill_id = $this->addSkill($skill_data);
        }
        $mem_skill_info = array('skill_id' => $skill_id, 
        'proficiency' => $member_proficiency);
        $this->saveStuSkillsInfo($mem_skill_info);
    }
    /**
     * Saves the test record(user point of view)
     * @todo
     * Enter description here ...
     */
    public function saveemptestrecordAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        //Zend_Registryget('logger')->debug($params);
        //Zend_Registryget('logger')->debug(
        //'params required are \'test_info\' ,  \'test_record\' , \'test_section_record\' in myarray ex \'$params[\'myarray\'][\'test_info\']');
        /*
         * in case of edit ,the $params['myarray']['test_record'] will contain test_record_id
         */
        $test_record = $params['myarray']['test_record'];
        $test_info = $params['myarray']['test_info'];
        $employability_test_id = $this->addEmpTest($test_info);
        $test_record['employability_test_id'] = $employability_test_id;
        $test_record_id = $this->saveEmpTestRecord($test_record);
        $section_record = $params['myarray']['test_section_record'];
        /* //Zend_Registryget('logger')->debug($test_info);
        //Zend_Registryget('logger')->debug($test_record);
        //Zend_Registryget('logger')->debug($section_record);*/
        foreach ($section_record as $section_id => $section_score) {
            $section_score['employability_test_id'] = $employability_test_id;
            $section_score['test_section_id'] = $section_id;
            $this->saveSectionScore($section_score);
        }
    }
    public function savelanguagesknownAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $member_id = null;
        //Zend_Registryget('logger')->debug(
        //'member_id may be sent in as parameter');
        if (empty($params['member_id'])) {
            $member_id = $this->getMember_id();
        } else {
            $member_id = $params['member_id'];
        }
        $is_new_language = $params['myarray']['new_language'];
        $language_info = $params['myarray']['language_info'];
        $member_proficiency = $params['myarray']['member_proficiency'];
        /*
         * language id sent by user
         */
        if (! empty($language_info['language_id'])) {
            $language_id = $language_info['language_id'];
        }
        /*
         * if language does not exist in databse add it, otherwise update member's proficiency
         */
        if ($is_new_language == 'true') {
            $language_info = array(
            'language_name' => $language_info['language_name']);
            /*
             * language id generated for new language and $language id updated
             */
            $language_id = $this->addLanguage($language_info);
        }
        $proficiency = array();
        $can_speak = (($member_proficiency['SPEAK'] == 'true') ? ($proficiency[] = 'SPEAK') : null);
        $can_read = (($member_proficiency['READ'] == 'true') ? ($proficiency[] = 'READ') : null);
        $can_write = (($member_proficiency['WRITE'] == 'true') ? ($proficiency[] = 'WRITE') : null);
        $mem_lang_info = array('language_id' => $language_id, 
        'proficiency' => implode(',', $proficiency));
        $this->saveStuLanguageInfo($mem_lang_info);
    }
    public function savecocurricularAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $cocurricular_info = array();
        $cocurricular_info = $params['myarray']['cocurricular'];
        if (! empty($cocurricular_info)) {
            if (isset($cocurricular_info['achievements'])) {
                $member_cocurricular_info['achievements'] = $cocurricular_info['achievements'];
            }
            if (isset($cocurricular_info['activities'])) {
                $member_cocurricular_info['activities'] = $cocurricular_info['activities'];
            }
            if (isset($cocurricular_info['hobbies'])) {
                $member_cocurricular_info['hobbies'] = $cocurricular_info['hobbies'];
            }
            $this->saveCourricularInfo($member_cocurricular_info);
        }
    }
    public function savecertificationAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        //Zend_Registryget('logger')->debug($params);
        $student_certification = $params['myarray']['student_certification'];
        $certification_info = $params['myarray']['certification_info'];
        $certification_id = $this->saveCertificationInfo($certification_info);
        $student_certification['certification_id'] = $certification_id;
        //Zend_Registryget('logger')->debug($student_certification);
        $this->saveStuCertificationInfo($student_certification);
    }
    public function savetrainingAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $training_info = $params['myarray']['training_info'];
        $functional_area_info = $params['myarray']['functional_area_info'];
        $training_info['functional_area_id'] = $functional_area_info['functional_area_id'];
        $this->saveStuTrainingInfo($training_info);
    }
    public function saveexperienceAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        //Zend_Registryget('logger')->debug($params);
        $experience_info = $params['myarray']['experience_info'];
        $student_experience = $params['myarray']['student_experience'];
        $role_name = $experience_info['role_name'];
        $industry_name = $experience_info['industry_name'];
        $functional_area_name = $experience_info['functional_area_name'];
        if ($role_name) {
            $role_array = array('role_name' => $role_name);
            $role_id = $this->saveRoleInfo($role_array);
            $student_experience['role_id'] = $role_id;
        }
        if ($industry_name) {
            $industry_array = array('industry_name' => $industry_name);
            $industry_id = $this->saveIndustryInfo($industry_array);
            $student_experience['industry_id'] = $industry_id;
        }
        if ($functional_area_name) {
            $functional_array = array(
            'functional_area_name' => $functional_area_name);
            $functional_area_id = $this->saveFunctionalAreaInfo(
            $functional_array);
            $student_experience['functional_area_id'] = $functional_area_id;
        }
        $this->saveExperienceInfo($student_experience);
    }
    public function savejobpreferredAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $params = array_diff($request->getParams(), $request->getUserParams());
        $job_preference = $params['myarray']['job_area_name'];
        $info['job_area'] = $job_preference;
        $this->saveJobPreferred($info);
    }
    /* ------------------------------------------------------------------------------------------- */
    /*********************************************************************************************/
    /**
     * Checks if member is registered in the core,
     * @return true if member_id is registered, false otherwise
     */
    private function memberIdCheck ($member_id_to_check)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id_to_check);
        $member_id_exists = $student->memberIdCheck();
        if (! $member_id_exists) {
            //Zend_Registryget('logger')->debug(
        //'Member with member_id : ' . $member_id_to_check .
        // ' is not registered in CORE');
        }
        return $member_id_exists;
    }
    /**
     * before calling this function use memberidcheck function
     * Enter description here ...
     * @param int $member_id
     */
    private function findCriticalInfo ($member_id)
    {
        $member_id_exists = $this->memberIdCheck($member_id);
        if ($member_id_exists) {
            $student = new Tnp_Model_Member_Student();
            $student->setMember_id($member_id);
            $student = $student->fetchCriticalInfo();
            if ($student instanceof Tnp_Model_Member_Student) {
                $critical_data['member_id'] = $member_id;
                $critical_data['first_name'] = $student->getFirst_name();
                $critical_data['middle_name'] = $student->getMiddle_name();
                $critical_data['last_name'] = $student->getLast_name();
                $critical_data['cast'] = $student->getCast_name();
                $critical_data['nationality'] = $student->getNationality_name();
                $critical_data['religion'] = $student->getReligion_name();
                $critical_data['blood_group'] = $student->getBlood_group();
                $critical_data['dob'] = $student->getDob();
                $critical_data['gender'] = $student->getGender();
                $critical_data['member_type_id'] = $student->getMember_type_id();
                $critical_data['religion_id'] = $student->getReligion_id();
                $critical_data['nationality_id'] = $student->getNationality_id();
                $critical_data['cast_id'] = $student->getCast_id();
                return $critical_data;
            }
        }
    }
    private function getEmpTestRecordInfo ($test_record_id, $member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $emp_test_record = array();
        $info = $student->fetchEmpTestRecordInfo($test_record_id);
        if ($info instanceof Tnp_Model_MemberInfo_EmployabilityTestRecord) {
            $emp_test_record['employability_test_id'] = $info->getEmployability_test_id();
            $emp_test_record['test_regn_no'] = $info->getTest_regn_no();
            $emp_test_record['test_total_score'] = $info->getTest_total_score();
            $emp_test_record['test_percentile'] = $info->getTest_percentile();
        } else {
            $emp_test_record = false;
        }
        return $emp_test_record;
    }
    private function getEmpTestInfo ($employability_test_id)
    {
        $emp_test = new Tnp_Model_EmpTestInfo_Test();
        $emp_test_info = array();
        $emp_test->setEmployability_test_id($employability_test_id);
        $info = $emp_test->fetchInfo();
        if ($info instanceof Tnp_Model_EmpTestInfo_Test) {
            $emp_test_info['test_name'] = $info->getTest_name();
            $emp_test_info['date_of_conduct'] = $info->getDate_of_conduct();
        } else {
            $emp_test_info = false;
        }
        return $emp_test_info;
    }
    private function getEmpTestRecordIds ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        return $student->fetchEmpTestRecordIds();
    }
    private function getEmpTestRecordId ($member_id, $employability_test_id)
    {
        $test = new Tnp_Model_MemberInfo_EmployabilityTestRecord();
        $test->setMember_id($member_id);
        $test->setEmployability_test_id($employability_test_id);
        return $test->fetchTestRecordIds(true, true);
    }
    private function generateEmpTestRecords ($member_id)
    {
        Zend_Registry::get('logger')->debug($member_id);
        $record_info = array();
        $record_ids = $this->getEmpTestRecordIds($member_id);
        if (! empty($record_ids)) {
            foreach ($record_ids as $key => $test_record_id) {
                $raw_info = $this->getEmpTestRecordInfo($test_record_id, 
                $member_id);
                $employability_test_id = $raw_info['employability_test_id'];
                $record_info[$test_record_id] = $raw_info;
                $test_info = $this->getEmpTestInfo($employability_test_id);
                $record_info[$test_record_id]['test_name'] = $test_info['test_name'];
                $record_info[$test_record_id]['date_of_conduct'] = $test_info['date_of_conduct'];
            }
        } else {
            $record_info = false;
        }
        return $record_info;
    }
    /*********************************************************************************************/
    private function getTestSectionIds ($employability_test_id)
    {
        $section = new Tnp_Model_EmpTestInfo_Section();
        $section->setEmployability_test_id($employability_test_id);
        return $section->fetchTestSectionIds(true);
    }
    private function getTestSectionInfo ($test_section_id)
    {
        $emp_sec = new Tnp_Model_EmpTestInfo_Section();
        $emp_sec_info = array();
        $emp_sec->setTest_section_id($test_section_id);
        $info = $emp_sec->fetchInfo();
        if ($info instanceof Tnp_Model_EmpTestInfo_Section) {
            $emp_sec_info['test_section_name'] = $info->getTest_section_name();
        } else {
            $emp_sec_info = false;
        }
        return $emp_sec_info;
    }
    private function getTestSectionScoreIds ($employability_test_id, $member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        return $student->fetchEmpTestSectionScoreIds($employability_test_id);
    }
    private function getSectionScoreInfo ($section_score_id, $member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $info = $student->fetchEmpTestSectionScoreInfo($section_score_id);
        $section_score_info = array();
        if ($info instanceof Tnp_Model_MemberInfo_EmployabilityTestSectionScore) {
            $section_score_info['test_section_id'] = $info->getTest_section_id();
            $section_score_info['section_marks'] = $info->getSection_marks();
            $section_score_info['section_percentile'] = $info->getSection_percentile();
        } else {
            $section_score_info = false;
        }
        return $section_score_info;
    }
    private function generateSectionScore ($employability_test_id, $member_id)
    {
        $score_info = array();
        $score_ids = $this->getTestSectionScoreIds($employability_test_id, 
        $member_id);
        Zend_Registry::get('logger')->debug($employability_test_id);
        Zend_Registry::get('logger')->debug($score_ids);
        if (! empty($score_ids)) {
            foreach ($score_ids as $key => $section_score_id) {
                $raw_info = $this->getSectionScoreInfo($section_score_id, 
                $member_id);
                $test_section_id = $raw_info['test_section_id'];
                $score_info[$test_section_id] = $raw_info;
                $section_info = $this->getTestSectionInfo($test_section_id);
                $score_info[$test_section_id]['test_section_name'] = $section_info['test_section_name'];
            }
        } else {
            $score_info = false;
        }
        return $score_info;
    }
    private function generateCertificationInfo ($member_id)
    {
        $certification_ids = array();
        $certification_ids = $this->findCertificationIds($member_id);
        if (empty($certification_ids)) {
            return false;
        } else {
            $certification_info = array();
            foreach ($certification_ids as $certification_id) {
                $certification_info[$certification_id] = $this->findCertificationsInfo(
                $member_id, $certification_id);
            }
            return $certification_info;
        }
    }
    private function findCertificationIds ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $student_certifications = array();
        return $student_certification_ids = $student->fetchCertificationIds();
    }
    private function findCertificationsInfo ($member_id, $certification_id)
    {
        $certi = new Tnp_Model_Certification();
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $certification = $student->fetchCertificationInfo($certification_id);
        if ($certification instanceof Tnp_Model_MemberInfo_Certification) {
            $student_certifications = array();
            $certi->setCertification_id($certification_id);
            $certi_info = $certi->fetchInfo();
            if ($certi_info instanceof Tnp_Model_Certification) {
                $certi_name = $certi_info->getCertification_name();
                $func_id = $certi_info->getFunctional_area_id();
                $student_certifications['functional_area_id'] = $func_id;
                $func_area_name = $this->findFunctionAreaName($func_id);
            }
            $student_certifications['certification_name'] = $certi_name;
            $student_certifications['functional_area_name'] = $func_area_name;
            $student_certifications['start_date'] = $certification->getStart_date();
            $student_certifications['description'] = $certification->getDescription();
            $student_certifications['complete_date'] = $certification->getComplete_date();
        } else {
            $student_certifications = false;
        }
        return $student_certifications;
    }
    private function findFunctionAreaName ($func_id)
    {
        $func_area = new Tnp_Model_FunctionalArea();
        $func_area->setFunctional_area_id($func_id);
        $func_info = $func_area->fetchInfo();
        if ($func_info instanceof Tnp_Model_FunctionalArea) {
            return $func_info->getFunctional_area_name();
        }
    }
    private function generateTrainingInfo ($member_id)
    {
        $training_ids = array();
        $training_ids = $this->findTrainingIds($member_id);
        if (empty($training_ids)) {
            return false;
        } else {
            $training_info = array();
            foreach ($training_ids as $training_id) {
                $training_info[$training_id] = $this->findTrainingInfo(
                $member_id, $training_id);
            }
            return $training_info;
        }
    }
    private function findTrainingIds ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $training_ids = array();
        return $student->fetchTrainingIds();
    }
    private function findTrainingInfo ($member_id, $training_id)
    {
        $student = new Tnp_Model_Member_Student();
        $functional_area = new Tnp_Model_FunctionalArea();
        $student->setMember_id($member_id);
        $training = $student->fetchTrainingInfo($training_id);
        if ($training instanceof Tnp_Model_MemberInfo_Training) {
            $student_training = array();
            $functional_area_id = $training->getFunctional_area_id();
            $func_area_name = $this->findFunctionAreaName($functional_area_id);
            $student_training['functional_area_name'] = $func_area_name;
            $student_training['functional_area_id'] = $func_area_name;
            $student_training['training_institute'] = $training->getTraining_institute();
            $student_training['start_date'] = $training->getStart_date();
            $student_training['completion_date'] = $training->getCompletion_date();
            $student_training['training_semester'] = $training->getTraining_semester();
            $student_training['grade'] = $training->getGrade();
            $student_training['description'] = $training->getDescription();
            return $student_training;
        } else {
            return false;
        }
    }
    private function findExperienceInfo ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $student_experience = array();
        $student_experience_ids = $student->fetchExperienceIds();
        $experience = new Tnp_Model_MemberInfo_Experience();
        if (! empty($student_experience_ids)) {
            foreach ($student_experience_ids as $key => $student_experience_id) {
                $experience->setStudent_experience_id($student_experience_id);
                $experience->fetchInfo();
                $student_experience[$student_experience_id]['organisation'] = $experience->getOrganisation();
                $student_experience[$student_experience_id]['industry_id'] = $experience->getIndustry_id();
                $student_experience[$student_experience_id]['functional_area_id'] = $experience->getFunctional_area_id();
                $student_experience[$student_experience_id]['role_id'] = $experience->getRole_id();
                $student_experience[$student_experience_id]['experience_months'] = $experience->getExperience_months();
                $student_experience[$student_experience_id]['experience_years'] = $experience->getExperience_years();
                $student_experience[$student_experience_id]['organisation'] = $experience->getOrganisation();
                $student_experience[$student_experience_id]['start_date'] = $experience->getStart_date();
                $student_experience[$student_experience_id]['end_date'] = $experience->getEnd_date();
                $student_experience[$student_experience_id]['is_parttime'] = $experience->getIs_parttime();
                $student_experience[$student_experience_id]['description'] = $experience->getDescription();
            }
        } else {
            $student_experience = false;
        }
        return $student_experience;
    }
    private function findCocurricularInfo ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $co_curr_array = array();
        $student_co_curr = new Tnp_Model_MemberInfo_CoCurricular();
        $student_co_curr->setMember_id($member_id);
        $info = $student_co_curr->fetchInfo();
        if ($info instanceof Tnp_Model_MemberInfo_CoCurricular) {
            $co_curr_array['achievements'] = $student_co_curr->getAchievements();
            $co_curr_array['activities'] = $student_co_curr->getActivities();
            $co_curr_array['hobbies'] = $student_co_curr->getHobbies();
        } else {
            $co_curr_array = false;
        }
        return $co_curr_array;
    }
    private function findSkillsInfo ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $skill_ids = $student->fetchSkillsIds();
        $skill_info = array();
        if (! empty($skill_ids)) {
            $skill_object = new Tnp_Model_Skill();
            foreach ($skill_ids as $skill_id) {
                $skill_object->setSkill_id($skill_id);
                $prof = $student->fetchSkillInfo($skill_id);
                if ($prof instanceof Tnp_Model_MemberInfo_Skills) {
                    $proficiency = $prof->getProficiency();
                }
                $skill_object->fetchInfo();
                $skill_info[$skill_id] = array(
                'skill_name' => $skill_object->getSkill_name(), 
                'proficiency' => $proficiency);
            }
        } else {
            $skill_info = false;
        }
        return $skill_info;
    }
    private function findLanguageInfo ($member_id)
    {
        $mem_language = new Tnp_Model_MemberInfo_Language();
        $mem_language->setMember_id($member_id);
        $info = $mem_language->fetchLanguagesInfo();
        if (! empty($info)) {
            $lang = new Tnp_Model_Language();
            $languages = $lang->fetchLanguages();
            $student_language_info = array();
            foreach ($info as $key => $proficiency) {
                $student_language_info[$key] = array(
                'language_name' => $languages[$key], 
                'proficiency' => $proficiency);
            }
        } else {
            $student_language_info = false;
        }
        return $student_language_info;
    }
    private function findJobPreferred ($member_id)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        $job_preferred = $student->fetchJobPreferred();
        return $job_preferred;
    }
    private function addEmpTest ($info)
    {
        $emp_test = new Tnp_Model_EmpTestInfo_Test();
        $test_details['test_name'] = $info['test_name'];
        $test_details['date_of_conduct'] = $info['date_of_conduct'];
        return $emp_test->save($test_details);
    }
    private function addEmpTestSection ($info)
    {
        $test_section = new Tnp_Model_EmpTestInfo_Section();
        $section_array = array();
        $section_array['test_section_name'] = $info['test_section_name'];
        $section_array['employability_test_id'] = $info['employability_test_id'];
        return $test_section->saveInfo($section_array);
    }
    private function saveSectionScore ($info)
    {
        $section_score['test_section_id'] = $info['test_section_id'];
        $section_score['employability_test_id'] = $info['employability_test_id'];
        $section_score['section_marks'] = $info['section_marks'];
        $section_score['section_percentile'] = $info['section_percentile'];
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $student->saveEmpTestSectionScore($section_score);
    }
    private function saveEmpTestRecord ($info)
    {
        $record = array();
        if (isset($info['test_record_id'])) {
            $record['test_record_id'] = $info['test_record_id'];
        }
        $record['employability_test_id'] = $info['employability_test_id'];
        $record['test_regn_no'] = $info['test_regn_no'];
        $record['test_total_score'] = $info['test_total_score'];
        $record['test_percentile'] = $info['test_percentile'];
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        return $student->saveEmpTestRecord($record);
    }
    private function saveTechFieldInfo ($info)
    {
        $tech_field = new Tnp_Model_FunctionalArea();
        $tech_info = array();
        $tech_info['technical_field_name'] = $info['technical_field_name'];
        $tech_info['technical_sector'] = $info['technical_sector'];
        return $tech_field->saveInfo($tech_info);
    }
    private function saveCertificationInfo ($info)
    {
        $certification = new Tnp_Model_Certification();
        $cert_info = array();
        $cert_info['certification_name'] = $info['certification_name'];
        $cert_info['functional_area_id'] = $info['functional_area_id'];
        return $certification->saveInfo($cert_info);
    }
    private function saveStuCertificationInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $cert_info = array();
        $cert_info['certification_id'] = $info['certification_id'];
        $cert_info['start_date'] = $info['start_date'];
        $cert_info['complete_date'] = $info['complete_date'];
        $cert_info['description'] = $info['description'];
        return $student->saveCertificationInfo($cert_info);
    }
    private function saveStuTrainingInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        //Zend_Registryget('logger')->debug($info);
        $training_info = array();
        if (! empty($info['training_id'])) {
            $training_info['training_id'] = $info['training_id'];
        }
        $training_info['functional_area_id'] = $info['functional_area_id'];
        $training_info['training_institute'] = $info['training_institute'];
        $training_info['start_date'] = $info['start_date'];
        $training_info['completion_date'] = $info['completion_date'];
        $training_info['training_semester'] = $info['training_semester'];
        $training_info['grade'] = $info['grade'];
        $training_info['description'] = $info['description'];
        return $student->saveTrainingInfo($training_info);
    }
    private function saveIndustryInfo ($info)
    {
        $industry = new Tnp_Model_Industry();
        $industry_info = array();
        $industry_info['industry_name'] = $info['industry_name'];
        return $industry->saveInfo($industry_info);
    }
    private function saveFunctionalAreaInfo ($info)
    {
        $functional_area = new Tnp_Model_FunctionalArea();
        $functional_area_info = array();
        $functional_area_info['functional_area_name'] = $info['functional_area_name'];
        return $functional_area->saveInfo($functional_area_info);
    }
    private function saveRoleInfo ($info)
    {
        $role = new Tnp_Model_Role();
        $role_info = array();
        $role_info['role_name'] = $info['role_name'];
        return $role->saveInfo($role_info);
    }
    private function saveExperienceInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $student_experience = array();
        $student_experience['industry_id'] = $info['industry_id'];
        $student_experience['functional_area_id'] = $info['functional_area_id'];
        $student_experience['role_id'] = $info['role_id'];
        $student_experience['experience_months'] = $info['experience_months'];
        $student_experience['experience_years'] = $info['experience_years'];
        $student_experience['organisation'] = $info['organisation'];
        $student_experience['start_date'] = $info['start_date'];
        $student_experience['end_date'] = $info['end_date'];
        $student_experience['is_parttime'] = $info['is_parttime'];
        $student_experience['description'] = $info['description'];
        return $student->saveExperienceInfo($student_experience);
    }
    private function addLanguage ($info)
    {
        $language = new Tnp_Model_Language();
        $language_info = array();
        $language_info['language_name'] = $info['language_name'];
        return $language->saveInfo($language_info);
    }
    private function saveStuLanguageInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $language_info = array();
        $language_info['language_id'] = $info['language_id'];
        $language_info['proficiency'] = $info['proficiency'];
        return $student->saveLanguageInfo($language_info);
    }
    private function saveJobPreferred ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $job_preferred = $info['job_area'];
        return $student->saveJobAreaPreferred($job_preferred);
    }
    private function saveCourricularInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $co_curricular_info = array();
        $co_curricular_info['achievements'] = $info['achievements'];
        $co_curricular_info['activities'] = $info['activities'];
        $co_curricular_info['hobbies'] = $info['hobbies'];
        return $student->saveCoCurricularInfo($co_curricular_info);
    }
    private function addSkill ($info)
    {
        $skills = new Tnp_Model_Skill();
        $skill_info = array();
        $skill_info['skill_name'] = $info['skill_name'];
        $skill_info['skill_field'] = $info['skill_field'];
        return $skills->saveInfo($skill_info);
    }
    private function saveStuSkillsInfo ($info)
    {
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($this->getMember_id());
        $skill_info = array();
        $skill_info['skill_id'] = $info['skill_id'];
        $skill_info['proficiency'] = $info['proficiency'];
        return $student->saveSkillInfo($skill_info);
    }
    /**
     * @todo view changes no class finder
     * Enter description here ...
     * @param unknown_type $data_to_save
     */
    private function saveClassData ($class_info)
    {
        $member_id = $this->getMember_id();
        $class_info['member_id'] = $member_id;
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        return $student->saveClassInfo($class_info);
    }
    private function saveCriticalData ($data_to_save)
    {
        /**
         * 
         * static for student
         * @var int
         */
        $member_id = $this->getMember_id();
        $data_to_save['member_type_id'] = 1;
        $student_model = new Tnp_Model_Member_Student();
        $student_model->setMember_id($member_id);
        return $student_model->saveCriticalInfo($data_to_save);
    }
    private function deleteSkill ($member_id, $skill_id)
    {
        $member_skills = new Tnp_Model_MemberInfo_Skills();
        $member_skills->setSkill_id($skill_id);
        $member_skills->setMember_id($member_id);
        $member_skills->deleteSkill();
    }
    private function deleteEmpTestRecord ($test_record_id)
    {
        $e_t_record = new Tnp_Model_MemberInfo_EmployabilityTestRecord();
        $e_t_record->setTest_record_id($test_record_id);
        $e_t_record->deleteRecord();
    }
    private function deleteLanguageKnown ($member_id, $language_id)
    {
        $language = new Tnp_Model_MemberInfo_Language();
        $language->setMember_id($member_id);
        $language->setLanguage_id($language_id);
        $language->deleteLanguageKnown();
    }
    private function deleteCoCurricular ($member_id)
    {
        $co_curricular = new Tnp_Model_MemberInfo_CoCurricular();
        $co_curricular->setMember_id($member_id);
        $co_curricular->deleteCoCurricular();
    }
    private function deletejobpreferred ($member_id)
    {
        $job = new Tnp_Model_MemberInfo_JobPreferred();
        $job->setMember_id($member_id);
        return $job->deleteJobPreferrence();
    }
    private function deletetraining ($member_id, $training_id)
    {
        $training = new Tnp_Model_MemberInfo_Training();
        $training->setMember_id($member_id);
        $training->setTraining_id($training_id);
        return $training->deleteTrainingRecord();
    }
    private function deleteStuCertification ($member_id, $certification_id)
    {
        $certification = new Tnp_Model_MemberInfo_Certification();
        $certification->setMember_id($member_id);
        $certification->setCertification_id($certification_id);
        return $certification->deleteStuCertification();
    }
    private function fetchSkills ()
    {
        $skill_object = new Tnp_Model_Skill();
        return $skill_object->fetchSkills();
    }
    private function fetchLanguages ()
    {
        $lang_object = new Tnp_Model_Language();
        return $lang_object->fetchLanguages();
    }
    private function fetchFunctionalAreas ()
    {
        $func_area = new Tnp_Model_FunctionalArea();
        return $func_area->fetchFunctionalAreas();
    }
    private function fetchCertifications ()
    {
        $certification = new Tnp_Model_Certification();
        return $certification->fetchCertifications();
    }
    /**
     * 
     * Enter description here ...
     * @param array $data_to_save
     */
    private function saveClassInfo ($member_id, $class_info)
    {
        $class_info['member_id'] = $member_id;
        $student = new Tnp_Model_Member_Student();
        $student->setMember_id($member_id);
        return $student->saveClassInfo($class_info);
    }
    private function exportToExcel ($headers, $data, $file_id)
    {
        $php_excel = new PHPExcel();
        $php_excel->getProperties()
            ->setCreator("AMBALA COLLEGE")
            ->setLastModifiedBy("AMBALA COLLEGE")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Contains crucial student data")
            ->setKeywords("office 2007 openxml php");
        $excel_sheet = $php_excel->getActiveSheet();
        $alphabets = range('A', 'Z');
        $inc = 0;
        for ($p = 26; $p < 50; $p ++) {
            $alphabets[$p] = 'A' . $alphabets[$inc];
            $inc ++;
        }
        $styleArray = array('font' => array('bold' => true, 'size' => 12), 
        'alignment' => array(
        'center' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER), 
        'borders' => array(
        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR, 
        'rotation' => 90, 'startcolor' => array('argb' => 'FFA0A0A0'), 
        'endcolor' => array('argb' => 'FFFFFFFF')));
        $excel_sheet->getStyle("A1:Z1")->applyFromArray($styleArray);
        $excel_sheet->getStyle("AA1:AF1")->applyFromArray($styleArray);
        $alphabets_index = 0;
        $row_number = 1;
        foreach ($headers as $header) {
            $cell_coordinate = $alphabets[$alphabets_index] . $row_number;
            $excel_sheet->setCellValue($cell_coordinate, 
            strtoupper(' ' . $header . ' '));
            $alphabets_index = ($alphabets_index + 1);
        }
        foreach ($alphabets as $alphabet) {
            $excel_sheet->getColumnDimension($alphabet)->setAutoSize(true);
        }
        $data_to_export = array();
        foreach ($data as $key => $row) {
            foreach ($row as $col => $value) {
                $data_to_export[$key][utf8_decode($col)] = utf8_decode($value);
            }
        }
        $row_number = 2;
        foreach ($data_to_export as $student_data) {
            $index_to_get = 0;
            foreach ($student_data as $info) {
                if (empty($value)) {
                    $value = 'NA';
                }
                $coordinate = $alphabets[$index_to_get];
                $excel_sheet->setCellValue($coordinate . $row_number, 
                ' ' . $info . ' ');
                $index_to_get = ($index_to_get + 1);
            }
            $row_number += 1;
        }
        $php_excel->setActiveSheetIndex(0);
        $filename = DATA_EXCEL . "/Student_Data-" . $file_id . ".xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($php_excel, 'Excel2007');
        $objWriter->save($filename);
    }
}
?>
    