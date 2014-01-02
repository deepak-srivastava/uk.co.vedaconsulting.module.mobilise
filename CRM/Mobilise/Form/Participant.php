<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */

/**
 * 
 *
 */
class CRM_Mobilise_Form_Participant extends CRM_Mobilise_Form_Mobilise {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_mtype = $this->get('mtype');
    $this->assign('participant_fields', $this->_metadata[$this->_mtype]['participant_fields']);

    $cidList = implode(",", $this->get('cids'));
    $cidList = CRM_Utils_Type::escape($cidList, 'String');
    $query   = "
SELECT cc.id, cc.sort_name 
FROM civicrm_contact cc
WHERE cc.id IN ({$cidList})";
    $dao     = CRM_Core_DAO::executeQuery($query);
    $contacts = array();
    while ($dao->fetch()) {
      $contacts[$dao->id] = $dao->toArray();
    }
    $this->assign('contacts', $contacts);

    $rolesList = array_flip(CRM_Event_PseudoConstant::participantRole());
    $alumniRoles = $this->_metadata[$this->_mtype]['participant_fields']['role'];
    $this->_alumniRoleIDs = array();
    foreach ($alumniRoles as $role) {
      if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
        $this->_alumniRoleIDs[] = $roleID;
      }
    }
    $staffRoles = $this->_metadata[$this->_mtype]['participant_fields']['staff_contact'];
    $this->_staffRoleIDs = array();
    foreach ($staffRoles as $role) {
      if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
        $this->_staffRoleIDs[] = $roleID;
      }
    }
    $studentRoles = $this->_metadata[$this->_mtype]['participant_fields']['student_contact'];
    $this->_studentRoleIDs = array();
    foreach ($studentRoles as $role) {
      if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
        $this->_studentRoleIDs[] = $roleID;
      }
    }
    if (empty($this->_staffRoleIDs)) {
      CRM_Core_Error::fatal(ts('Staff Contact roles missing.'));
    }
    if (empty($this->_studentRoleIDs)) {
      CRM_Core_Error::fatal(ts('Student Contact roles missing.'));
    }
    parent::preProcess();
  }

  /**
   * This function sets the default values for the form in edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  public function setDefaultValues() {
    $defaults = array();
    list($defaults['register_date'], $defaults['register_date_time']) = CRM_Utils_Date::setDateDefaults(NULL, 'activityDateTime');

    $defaults['role_id'] = array();
    foreach ($this->_alumniRoleIDs as $roleID) {
      $defaults['role_id'][$roleID] = 1;
    }
    if (!empty($defaults['role_id'])) {
      foreach ($this->_roleTypes as $element) {
        $element->freeze();
      }
    }
    return $defaults;
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    if (array_key_exists('role', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $roleTypes = array();
      $roleids   = CRM_Event_PseudoConstant::participantRole();
      foreach ($roleids as $rolekey => $rolevalue) {
        $this->_roleTypes[] = 
          $this->createElement('checkbox', $rolekey, NULL, $rolevalue);
      }
      $this->addGroup($this->_roleTypes, 'role_id', ts('Participant Role'));
      $this->addRule('role_id', ts('Role is required'), 'required');
    }
    if (in_array('register_date', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $this->addDateTime('register_date', ts('Registration Date'), TRUE, array('formatType' => 'activityDateTime'));
    }
    if (in_array('status', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $status = CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label');
      $this->add('select', 'status_id', ts('Participant Status'), 
        array('' => ts('- select -')) + $status, TRUE);
    }
    if (in_array('source', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $this->add('text', 'source', ts('Event Source'));
    }
    if (array_key_exists('staff_contact', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $this->add('text', "contact[1]", ts('Staff Contact'), array('width' => '200px'), TRUE);
      $this->addElement('hidden', "contact_select_id[1]");
    }
    if (array_key_exists('student_contact', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $this->add('text', "contact[2]", ts('Student Contact'), array('width' => '200px'), TRUE);
      $this->addElement('hidden', "contact_select_id[2]");
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    require_once 'api/api.php';
    $values = $this->controller->exportValues($this->_name);

    foreach ($this->get('cids') as $cid) {
      if (CRM_Utils_Type::validate($cid, 'Integer')) {
        $params = 
          array( 
            'contact_id'  => $cid,
            'event_id'    => $this->get('event_id'),
            'status_id'   => $values['status_id'],
            'role_id'     => implode(CRM_Core_DAO::VALUE_SEPARATOR, array_keys($values['role_id'])),
            'register_date' => CRM_Utils_Date::processDate($values['register_date'], $values['register_date_time']),
            'source'        => $values['source'],
            'version'       => 3,
          );
        $result = civicrm_api( 'participant','create',$params );
      }
    }
    if (!empty($values['contact_select_id'])) {
      foreach ($values['contact_select_id'] as $key => $cid) {
        $roleIDs = ($key == 1) ? $this->_staffRoleIDs : ($key == 2 ? $this->_studentRoleIDs : array());
        $roleIDs = implode(CRM_Core_DAO::VALUE_SEPARATOR, $roleIDs);
        if (!empty($roleIDs) && CRM_Utils_Type::validate($cid, 'Integer')) {
          $params = 
            array( 
              'contact_id'  => $cid,
              'event_id'    => $this->get('event_id'),
              'status_id'   => $values['status_id'],
              'role_id'     => $roleIDs,
              'register_date' => CRM_Utils_Date::processDate($values['register_date'], $values['register_date_time']),
              'source'        => $values['source'],
              'version'       => 3,
            );
          $result = civicrm_api( 'participant','create',$params );
        }
      }
    }
  }

  /**
   * Display Name of the form
   *
   * @access public
   *
   * @return string
   */
  public function getTitle() {
    return ts('Assign Alumni');
  }
}

