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
class CRM_Mobilise_Form_NewEvent extends CRM_Mobilise_Form_Mobilise {

  protected $_eventID = NULL;
 
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
    $this->assign('event_fields', $this->_metadata[$this->_mtype]['event_fields']);

    if ($this->_id) {
      if (!$this->get('event_id')) {
        CRM_Core_Error::fatal(ts("Couldn't determine the Event."));
      }
      $this->_eventID = $this->get('event_id');
    }
  }

  /**
   * This function sets the default values for the form. For edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    $defaults = array();

    if (isset($this->_id)) {
      $defaults = CRM_Custom_Form_CustomData::setDefaultValues($this);
      $params   = array('id' => $this->_eventID);
      CRM_Event_BAO_Event::retrieve($params, $defaults);
      list($defaults['start_date']) = 
        CRM_Utils_Date::setDateDefaults($defaults['start_date'], 'activityDate');
      if (!empty($defaults['end_date'])) {
        list($defaults['end_date']) = 
          CRM_Utils_Date::setDateDefaults($defaults['end_date'], 'activityDate');
      }
    } else {
      list($defaults['start_date']) = 
        CRM_Utils_Date::setDateDefaults(NULL, 'activityDate');
      if ($this->_eventTypeId) {
        $defaults['event_type_id'] = $this->_eventTypeId;
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
    if (array_key_exists('type', $this->_metadata[$this->_mtype]['event_fields'])) {
      $eventTypes = CRM_Core_OptionGroup::values('event_type');
      $element    = 
        $this->add('select', 'event_type_id', ts('Event Type'),
          array('' => ts('- select -')) + $eventTypes, TRUE);
      if ($this->_eventTypeId) {
        $element->freeze();
      }
    }
    if (in_array('name', $this->_metadata[$this->_mtype]['event_fields'])) {
      $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
      $this->add('text', 'title', ts('Event Name'), $attributes['event_title'], TRUE);
    }
    if (in_array('start_date', $this->_metadata[$this->_mtype]['event_fields'])) {
      $this->addDate('start_date', ts('Start Date'), TRUE, array('formatType' => 'activityDate'));
    }
    if (in_array('end_date', $this->_metadata[$this->_mtype]['event_fields'])) {
      $this->addDate('end_date', ts('End Date'), FALSE, array('formatType' => 'activityDate'));
    }

    // custom handling
    if (array_key_exists('custom', $this->_metadata[$this->_mtype]['event_fields'])) {
      $this->set('type', 'Event');
      //FIXME: uncomment subType when we have event-type known
      $this->set('subType',  $this->_eventTypeId);
      $this->set('entityID', $this->_eventID);
      $this->set('cgcount',  1);
      CRM_Custom_Form_CustomData::preProcess($this);
      foreach ($this->_groupTree as $gID => &$grpVals) {
        foreach ($grpVals['fields'] as $fID => &$fldVals) {
          if ($fldVals['label'] == CRM_Mobilise_Form_Mobilise::SCHOOL_HOST_CUSTOM_FIELD_TITLE && 
            $grpVals['title'] == CRM_Mobilise_Form_Mobilise::SCHOOL_CUSTOM_SET_TITLE) {
              $this->_hostSchoolElement = $fldVals['element_name'];
            }
          if (!in_array($fldVals['label'], $this->_metadata[$this->_mtype]['event_fields']['custom'])) {
            unset($grpVals['fields'][$fID]);
          }
        }
        if (empty($grpVals['fields'])) {
          unset($this->_groupTree[$gID]);
        }
      }
      CRM_Custom_Form_CustomData::buildQuickForm($this);
    }
    parent::buildQuickForm();
    
    $this->addFormRule(array('CRM_Mobilise_Form_NewEvent', 'formRule'), $this);
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

    if ($endDate = CRM_Utils_Array::value('end_date', $fields)) {
      $fromDate = CRM_Utils_Date::processDate(CRM_Utils_Array::value('start_date', $fields));
      $endDate  = CRM_Utils_Date::processDate($endDate);
      if ($endDate < $fromDate) {
        $errors['end_date'] = ts("End Date can't be earlier than the Start Date.");
      }
    }

    return $errors;
  }

  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);

    //format params
    if ($this->_eventID) {
      $params['id'] = $this->_eventID;
    }
    $params['start_date'] = CRM_Utils_Date::processDate($params['start_date']);
    $params['end_date']   = CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $params), '235959', TRUE);
    $params['is_active']  = CRM_Utils_Array::value('is_active', $params, 1);

    if ($this->_hostSchoolElement) {
      $params["{$this->_hostSchoolElement}"] = "sample text"; 
      // its the following user id that will be considered as contact-ref-id
      $params["{$this->_hostSchoolElement}_id"] = $this->_schoolId;
    } else {
      CRM_Core_Error::fatal(ts("Couldn't determine the custom field to hold school contact."));
    }

    // format custom params
    $params['custom'] = 
      CRM_Core_BAO_CustomField::postProcess($params,
        $customFields,
        $this->_eventID,
        'Event');
    // create event
    $event = CRM_Event_BAO_Event::create($params);
    $this->set('event_id', $event->id);
  }

  /**
   * Display Name of the form
   *
   * @access public
   *
   * @return string
   */
  public function getTitle() {
    $id = CRM_Utils_Request::retrieve('id', 'Positive', $this);
    return $id ? ts('Update Event') : ts('New Event');
  }
}

