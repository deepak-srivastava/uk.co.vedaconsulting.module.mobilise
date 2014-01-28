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
    parent::preProcess();

    $this->assign('participant_fields', 
      $this->_metadata[$this->_mtype]['participant_fields']);

    if (!$this->get('event_id')) {
      CRM_Core_Error::fatal(ts("Couldn't determine the Event."));
    }

    $rolesList = array_flip(CRM_Event_PseudoConstant::participantRole());
    $alumniRoles = $this->_metadata[$this->_mtype]['participant_fields']['role'];
    $this->_alumniRoleIDs = array();
    foreach ($alumniRoles as $role) {
      if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
        $this->_alumniRoleIDs[] = $roleID;
      }
    }
    $studentRoles = $this->_metadata[$this->_mtype]['participant_fields']['student_contact'];
    $this->_studentRoleIDs = array();
    if (!empty($studentRoles)) {
      foreach ($studentRoles as $role) {
        if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
          $this->_studentRoleIDs[] = $roleID;
        }
      }
    }
    if (empty($this->_studentRoleIDs) &&
       array_key_exists('student_contact', $this->_metadata[$this->_mtype]['participant_fields'])) {
      CRM_Core_Error::fatal(ts('Student Contact roles missing.'));
    }
    $this->_activityTypeId = $this->get('activity_type_id');

    if ($this->_id) {
      $this->_participants = array();
      require_once 'api/api.php';
      $params = 
        array( 
          'version'  => 3,
          'event_id' => $this->get('event_id'),
        );
      $result = civicrm_api('participant','get', $params);
      if (!$result['is_error'] && $result['count'] > 0) {
        $this->_participants = $result['values'];
      }
      if (empty($this->_participants)) {
        CRM_Core_Error::fatal(ts("Couldn't find any associated alumni with this mobilisation."));
      }
      $this->_targetContactIDs = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId($this->_id);
      if (empty($this->_targetContactIDs)) {
        CRM_Core_Error::fatal(ts("Couldn't find any alumni already associated with this mobilisation. OR this mobilisation no longer has any alumni associated."));
      }
    }
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

    $defaults['role_id'] = array();
    foreach ($this->_alumniRoleIDs as $roleID) {
      $defaults['role_id'][$roleID] = 1;
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
      $this->addGroup($this->_roleTypes, 'role_id', ts('Alumni Role'));
      $this->addRule('role_id', ts('Role is required'), 'required');
    }
    if (in_array('status', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $status = CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label');
      foreach ($status as $id => $label) {
        if (in_array(strtolower($label), 
            array("pending from pay later", "pending from incomplete transaction", "pending in cart"))) {
          unset($status[$id]);
        }
      }
      $this->add('select', 'status_id', ts('Alumni Status'), 
        array('' => ts('- select -')) + $status, TRUE);
    }
    if (array_key_exists('student_contact', $this->_metadata[$this->_mtype]['participant_fields'])) {
      $this->add('text', "contact[1]", ts('Student Contact'), array('width' => '200px'), TRUE);
      $this->addElement('hidden', "contact_select_id[1]");
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    require_once 'api/api.php';
    $values = $this->controller->exportValues($this->_name);
    $count  = 0;
    $now    = date('YmdHis');

    if (!$this->_id) {
      // add action
      $targetContactIDs = array();
      foreach ($this->get('cids') as $cid) {
        if (CRM_Utils_Type::validate($cid, 'Integer')) {
          $targetContactIDs[] = $cid;
          $params = 
            array( 
              'contact_id'  => $cid,
              'event_id'    => $this->get('event_id'),
              'status_id'   => $values['status_id'],
              'role_id'     => implode(CRM_Core_DAO::VALUE_SEPARATOR, array_keys($values['role_id'])),
              'register_date' => $now,
              'version'       => 3,
            );
          $result = civicrm_api( 'participant','create', $params );
          if (!$result['is_error'] && $result['count'] > 0) {
            $count++;
          }
        }
      }

      if (!empty($values['contact_select_id'])) {
        foreach ($values['contact_select_id'] as $key => $cid) {
          $roleIDs = ($key == 1) ? $this->_studentRoleIDs : array();
          $roleIDs = implode(CRM_Core_DAO::VALUE_SEPARATOR, $roleIDs);
          if (!empty($roleIDs) && CRM_Utils_Type::validate($cid, 'Integer')) {
            $targetContactIDs[] = $cid;
            $params = 
              array( 
                'contact_id'  => $cid,
                'event_id'    => $this->get('event_id'),
                'status_id'   => $values['status_id'],
                'role_id'     => $roleIDs,
                'register_date' => $now,
                'version'       => 3,
              );
            $result = civicrm_api( 'participant','create', $params );
            if (!$result['is_error'] && $result['count'] > 0) {
              $count++;
            }
          }
        }
      }

      // record mobilisation activity
      $params = array(
        'status_id' => CRM_Core_OptionGroup::getValue('activity_status', 'Completed', 'name'),
        'source_contact_id' => $this->_schoolId,
        'source_record_id'  => $this->get('event_id'),
        'assignee_contact_id' => $this->_currentUserId,
        'target_contact_id'   => $targetContactIDs,
        'activity_type_id'    => $this->_activityTypeId,
        'activity_date_time'  => $now,
        'is_current_revision' => 0 ); // setting rev to 0, makes activity hidden on display 
      $activity = CRM_Activity_BAO_Activity::create($params);
    } else {
      // update action 
      $isStudentContactUpdated = FALSE;
      foreach ($this->_participants as $pid => $pdetails) {
        if (in_array($pdetails['contact_id'], $this->_targetContactIDs)) {
          if (!in_array($pdetails['participant_role_id'], $this->_studentRoleIDs)) {
            $params = 
              array( 
                'id'          => $pdetails['participant_id'],
                'status_id'   => $values['status_id'],
                'role_id'     => implode(CRM_Core_DAO::VALUE_SEPARATOR, array_keys($values['role_id'])),
              );
            $result = CRM_Event_BAO_Participant::create($params);
            if ($result->id) {
              $count++;
            }
            CRM_Core_Error::debug_log_message("Alumni updated: pid={$result->id}, cid={$result->contact_id}, sort-name={$pdetails['sort_name']}");
          } else if (!$isStudentContactUpdated && 
            !empty($values['contact_select_id'][1]) && 
            $pdetails['contact_id'] != $values['contact_select_id'][1]) {
            // for new student role contact we drop & create a new participant
            $params = 
              array( 
                'contact_id'  => $values['contact_select_id'][1],
                'event_id'    => $this->get('event_id'),
                'status_id'   => $values['status_id'],
                'role_id'     => implode(CRM_Core_DAO::VALUE_SEPARATOR, $this->_studentRoleIDs),
                'register_date' => $now,
                'version'       => 3,
              );
            $result = CRM_Event_BAO_Participant::create($params);
            if ($result->id) {
              CRM_Event_BAO_Participant::deleteParticipant($pdetails['participant_id']);
              $isStudentContactUpdated = TRUE; // only handle once
              CRM_Core_Error::debug_log_message("Alumni student role updated: pid={$result->id}, cid={$result->contact_id}");
            }
          }
        }
      }
    }

    if ($count > 0) {
      $statusMsg = $this->_id ? ts('Mobilisation successfully updated.') : 
        ts('Mobilisation successfully created for the selected alumni.');
    } else {
      $statusMsg = $this->_id ? ts('Could not update any mobilisations.') : 
        ts('Could not create any mobilisations.');
    }
    $this->set('status', $statusMsg);
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
