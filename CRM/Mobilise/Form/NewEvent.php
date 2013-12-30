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

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $mptype = $this->get('mtype');
    $this->assign('event_fields', $this->_metadata[$mptype]['event_fields']);

    parent::preProcess();
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

    list($defaults['start_date'], 
      $defaults['start_date_time']) = 
      CRM_Utils_Date::setDateDefaults(NULL, 'activityDateTime');
    $defaults['is_active'] = 1;

    return $defaults;
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $mptype = $this->get('mtype');

    if (in_array('type', $this->_metadata[$mptype]['event_fields'])) {
      $this->add('select', 'event_type_id', ts('Event Type'),
        array('' => ts('- select -')) + CRM_Core_OptionGroup::values('event_type'), TRUE);
    }
    if (in_array('name', $this->_metadata[$mptype]['event_fields'])) {
      $attributes = CRM_Core_DAO::getAttribute('CRM_Event_DAO_Event');
      $this->add('text', 'title', ts('Event Name'), $attributes['event_title']);
    }
    if (in_array('start_date', $this->_metadata[$mptype]['event_fields'])) {
      $this->addDateTime('start_date', ts('Start Date'), FALSE, array('formatType' => 'activityDateTime'));
    }
    if (in_array('end_date', $this->_metadata[$mptype]['event_fields'])) {
      $this->addDateTime('end_date', ts('End Date / Time'), FALSE, array('formatType' => 'activityDateTime'));
    }
    if (in_array('is_active', $this->_metadata[$mptype]['event_fields'])) {
      $this->addElement('checkbox', 'is_active', ts('Is this Event Active?'));
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);

    //format params
    $params['start_date'] = CRM_Utils_Date::processDate($params['start_date'], $params['start_date_time']);
    $params['end_date']   = CRM_Utils_Date::processDate(CRM_Utils_Array::value('end_date', $params),
      CRM_Utils_Array::value('end_date_time', $params),
      TRUE
    );
    $params['is_active']  = CRM_Utils_Array::value('is_active', $params, 1);

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
    return ts('New Event');
  }
}

