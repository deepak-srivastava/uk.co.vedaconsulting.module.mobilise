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
class CRM_Mobilise_Form_Event extends CRM_Mobilise_Form_Mobilise {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    parent::preProcess();

    $eventTypes = array_flip(CRM_Core_OptionGroup::values('event_type'));
    $this->_eventTypeId = CRM_Utils_Array::value($this->_metadata[$this->_mtype]['event_fields']['type'], $eventTypes);
    
    if (!$this->_eventTypeId) {
      CRM_Core_Error::fatal("Event Type '{$this->_metadata[$this->_mtype]['event_fields']['type']}' is not configured or present.");
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

    if ($this->_id) {
      $this->_eventID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'source_record_id');
      $defaults['event_id'] = $this->_eventID;
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
    $schoolTable  = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_CustomGroup', CRM_Mobilise_Form_Mobilise::SCHOOL_CUSTOM_SET_TITLE, 'table_name', 'title');
    $schoolColumn = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_CustomField', CRM_Mobilise_Form_Mobilise::SCHOOL_HOST_CUSTOM_FIELD_TITLE, 'column_name', 'label');
    $condition    = "( e.is_template IS NULL OR e.is_template != 1 )";
    if ($this->_eventTypeId) {
      $condition .= " AND e.event_type_id = {$this->_eventTypeId}";
    }

    $events = array();
    $query  = "
      SELECT e.id, e.title
        FROM civicrm_event e
  INNER JOIN $schoolTable s ON s.entity_id = e.id
       WHERE s.{$schoolColumn} = %1 AND e.is_active=1 AND {$condition}";
    $dao = CRM_Core_DAO::executeQuery($query, array(1 => array($this->_schoolId, 'Integer')));
    while ($dao->fetch()) {
      $events[$dao->id] = $dao->title;
    }
    $element = $this->add('select', 'event_id', ts('Select Event'), array('' => ts('- select event -')) + $events, FALSE);
    if ($this->_id) {
      $element->freeze();
    }

    $buttons = array(
      array('type' => 'next',
      'name' => $this->_id ? ts('Next >>') : ts('Use Selected Event'),
      'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
      'isDefault' => TRUE)
    );

    if (!$this->_id) {
      $buttons[] = 
        array('type' => 'next',
          'name' => ts('New Event'),
          'subName' => 'newevent',
          'spacing' => '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
        );
    }

    $buttons[] = 
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
      );
    $this->addButtons($buttons);

    $this->addFormRule(array('CRM_Mobilise_Form_Event', 'formRule'), $this);
  }

  /**
   * global form rule
   *
   * @param array $fields  the input form values
   * @param array $files   the uploaded files if any
   * @param array $options additional user data
   *
   * @return true if no errors, else array of errors
   * @access public
   * @static
   */
  static function formRule($fields, $files, $self) {
    $errors = array();

    $buttonClicked = $self->controller->getButtonName();
    if ($buttonClicked == '_qf_Event_next') {
      if(!$fields['event_id']) {
        $errors['event_id'] = ts("Please select an event OR click 'New Event' button to create one.");
      } else if (!$self->_id) { // for action = add
        $query = "SELECT contact_id FROM civicrm_participant WHERE event_id = %1 AND contact_id IN (". implode(",", $self->get('cids')) .")";
        $dao   = CRM_Core_DAO::executeQuery($query, array(1 => array($fields['event_id'], 'Integer')));
        if ($dao->fetch()) {
          if ($dao->N == count($self->get('cids'))) {
            $errors['event_id'] = ts("All of selected alumni, are already registered for this event.");
          } else if ($dao->N < count($self->get('cids'))) {
            $errors['event_id'] = ts("A few of selected alumni, are already registered for this event.");
          }
        }
      }
    }

    return $errors;
  }

  public function postProcess() {
    if ($this->_id) {
      $this->set('event_id', $this->_eventID);
      $this->controller->set('is_new_event', TRUE);
      return TRUE;
    }
    $buttonClicked = $this->controller->getButtonName();
    if ($buttonClicked == '_qf_Event_next_newevent') {
      $this->controller->set('is_new_event', TRUE);
    } else {
      $values = $this->controller->exportValues($this->_name);
      $this->set('event_id', $values['event_id']);
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
    return ts('Select or Create Mobilisation');
  }
}

