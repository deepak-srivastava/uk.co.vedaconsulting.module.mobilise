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
 * Base class to hold common behaviours for all steps of mobilisation wizard.
 */
class CRM_Mobilise_Form_Mobilise extends CRM_Core_Form {
  const
    SCHOOL_CUSTOM_SET_TITLE = 'School Events Data',
    SCHOOL_HOST_CUSTOM_FIELD_TITLE    = 'Hosting School',
    SCHOOL_STAFF_CUSTOM_FIELD_TITLE   = 'School Staff Contact',
    SCHOOL_NOTE_CUSTOM_FIELD_TITLE    = 'Notes',
    SCHOOL_SESSION_CUSTOM_FIELD_TITLE = 'Session Focus',
    ACTIVITY_CUSTOM_SET_TITLE           = 'Mobilisation Activity Data',
    ACTIVITY_AMOUNT_CUSTOM_FIELD_TITLE  = 'Amount',
    ACTIVITY_PURPOSE_CUSTOM_FIELD_TITLE = 'Purpose';
  
  protected $_metadata = 
    array(
      'Careers' => array( 
        'type'  => 'Event',
        'title' => 'Careers', 
        'event_fields' => array(
          'type' => 'School Careers',
          'name',
          'start_date',
          'custom' => array('School Staff Contact', 'Session Focus', 'Notes'),
        ),
        'participant_fields' => array(
          'role' => array('Speaker'), // roles for alumni
          'status',
        ),
      ),
      'Mentor' => array( 
        'type'  => 'Event',
        'title' => 'Mentor (Alumni)', 
        'event_fields' => array(
          'type' => 'School Mentoring',
          'name',
          'start_date',
          'end_date',
          'custom' => array('School Staff Contact', 'Notes'),
        ),
        'participant_fields' => array(
          'role' => array('Mentor (Alumni)'), // roles for alumni
          'status',
          'student_contact' => array('Attendee'), // roles for student-contact
        ),
      ),
      'Work Experience' => array( 
        'type'  => 'Event',
        'title' => 'Work Experience', 
        'event_fields' => array(
          'type' => 'School Work Experience',
          'name',
          'start_date',
          'end_date',
          'custom' => array('School Staff Contact', 'Notes'),
        ),
        'participant_fields' => array(
          'role' => array('Mentor (Alumni)'), // roles for alumni
          'status',
          'student_contact' => array('Attendee'), // roles for student-contact
        ),
      ),
      'Donation' => array( 
        'type'  => 'Activity',
        'title' => 'Donations / Fundraising', 
        'activity_fields' => array(
          'activity_type',
          'activity_date',
          'notes',
          'custom' => array('Amount', 'Purpose'),
        ),
      ),
      'Governor' => array( 
        'type'  => 'Activity',
        'title' => 'Governor', 
        'activity_fields' => array(
          'activity_type',
          'activity_date',
          'activity_end_date' => 'To Date',
          'notes',
          'custom' => array('To Date'), // end date is still custom, so we keep custom keyword
        ),
      ),
      'Non Careers' => array( 
        'type'  => 'Event',
        'title' => 'Non-Careers', 
        'event_fields' => array(
          'type' => 'School Non-Careers',
          'name',
          'start_date',
          'end_date',
          'custom' => array('School Staff Contact', 'Notes'),
        ),
        'participant_fields' => array(
          'role' => array(), // no predecided roles. user makes the choice
          'status',
        ),
      ),
      'Other' => array( 
        'type'  => 'Activity',
        'title' => 'Other', 
        'activity_fields' => array(
          'activity_type',
          'activity_date',
          'notes',
        ),
      ),
    );

  function getVar($name) {
    return isset($this->$name) ? $this->$name : NULL;
  }

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this);
    //FIXME: make sure act id has a type which is among the mtypes

    if ($this->_id) {
      // if we know id, initialize all required vars so each step of mobilisation could execute on its own.
      if (!$this->get('mtype')) {
        // set what step:type does on its completion
        $activityTypeID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'activity_type_id');
        $activityTypes  = CRM_Core_PseudoConstant::activityType();
        $this->set('mtype', $activityTypes[$activityTypeID]);
        $this->set('activity_type_id', $activityTypeID);
      }
      if (!$this->get('event_id')) {
        // set what step:event does on its completion
        $eventID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'source_record_id');
        $this->set('event_id', $eventID);
      }
      if (!$this->get('mtype')) {
        CRM_Core_Error::fatal(ts("Couldn't determine the mobilisation type. Something wrong with configurations."));
      }
    }
    $this->_mtype = $this->get('mtype');

    // logged in user
    $session = CRM_Core_Session::singleton();
    $this->_currentUserId = $session->get('userID');
    if (!$this->_id) {
      //FIXME: report instance id is hardcoded
      $session->pushUserContext(CRM_Utils_System::url('civicrm/report/instance/41', 'force=1'));
    }

    // school contact
    require_once 'CRM/Futurefirst/veda_FF_utils.php';
    $this->_schoolId = CRM_Futurefirst_veda_FF_utils::get_teacher_school_ID();
    if (!$this->_schoolId) {
      CRM_Core_Error::fatal(ts("Can't find the school contact."));
    }
    $this->assign('schoolId', $this->_schoolId);

    // build contact list
    $cids = CRM_Utils_Array::value('cids', $_REQUEST);
    if (empty($cids)) {
      $cids = $this->get('cids');
    } else {
      $contactIds = array();
      foreach ($cids as $cid) {
        // sanitize & validate input
        if (CRM_Utils_Type::validate($cid, 'Integer')) {
          $contactIds[] = $cid;
        }
      }
      $cids = $contactIds;
      if (!empty($cids)) {
        // fill cache if empty
        CRM_Contact_BAO_Contact_Permission::cache($this->_currentUserId);
        // check permission
        $query = "
          SELECT count(*)
            FROM civicrm_acl_contact_cache
           WHERE user_id = %1
             AND contact_id IN (" .implode(",", $cids). ")";
        $count = CRM_Core_DAO::singleValueQuery($query, array(1 => array($this->_currentUserId, 'Integer')));
        if ($count <> count($cids)) {
          CRM_Core_Error::statusBounce(ts("Permission Error: Non permissioned contact(s) / alumni selected."));
        }
        $this->set('cids', $cids);
      }
    }
    if (!$this->_id && empty($cids)) {
      CRM_Core_Error::statusBounce(ts("Could not find valid contact ids"));
    }
  }

  function getMobilisationTypes($types = array()) {
    $mobTypes = array();
    foreach ($this->_metadata as $key => $vals) {
      if (empty($types) || in_array($vals['type'], $types)) {
        $mobTypes[$key] = $vals['title'];
      }
    }
    return $mobTypes;
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $buttons = array(
      array('type' => 'next',
        'name' => ts('Next >>'),
        'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ),
    );
    $this->addButtons($buttons);
  }
}

