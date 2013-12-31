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
class CRM_Mobilise_Form_Target extends CRM_Mobilise_Form_Mobilise {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $mptype = $this->get('mtype');
    $this->assign('activity_fields', $this->_metadata[$mptype]['activity_fields']);

    $this->_activityTypeId = $this->get('activity_id');
    $activityTypes = CRM_Core_PseudoConstant::activityType(TRUE, FALSE, TRUE);
    if ($actType = CRM_Utils_Array::value($this->_activityTypeId, $activityTypes)) {
      $this->assign('activityType', $actType);
    } else {
      CRM_Core_Error::fatal("Can't determine activity type.");
    }

    $session = CRM_Core_Session::singleton();
    $this->_currentUserId = $session->get('userID');

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
    list($defaults['activity_date_time'], $defaults['activity_date_time_time']) = CRM_Utils_Date::setDateDefaults(NULL, 'activityDateTime');

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

    if (in_array('activity_date', $this->_metadata[$mptype]['activity_fields'])) {
      $this->addDateTime('activity_date_time', ts('Date'), TRUE, array('formatType' => 'activityDateTime'));
    }
    if (in_array('status', $this->_metadata[$mptype]['activity_fields'])) {
      $this->add('select', 'status_id', ts('Status'), CRM_Core_PseudoConstant::activityStatus(), TRUE);
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    $params  = $this->controller->exportValues($this->_name);

    $params['source_contact_id']  = $this->_currentUserId;
    $params['activity_type_id']   = $this->_activityTypeId;
    $params['activity_date_time'] = CRM_Utils_Date::processDate(
      $params['activity_date_time'], $params['activity_date_time_time']);
    
    foreach ($this->get('cids') as $cid) {
      if (CRM_Utils_Type::validate($cid, 'Integer')) {
        $params['target_contact_id']  = array($cid);
        $activity = CRM_Activity_BAO_Activity::create($params);
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
