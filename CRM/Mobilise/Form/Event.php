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
    $condition    = "( is_template IS NULL OR is_template != 1 )";
    if ($this->_eventTypeId) {
      $condition .= " AND event_type_id = {$this->_eventTypeId}";
    }

    $events = array();
    $query  = "
      SELECT e.id, e.title
      FROM civicrm_event e
      INNER JOIN $schoolTable s ON s.entity_id = e.id
      WHERE s.{$schoolColumn} = %1 AND {$condition}";
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
  }

  public function postProcess() {
    if ($this->_id) {
      $this->set('event_id', $this->_eventID);
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

