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
class CRM_Mobilise_Form_Type extends CRM_Mobilise_Form_Mobilise {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
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

    if ($this->_id) {
      $activityTypes = CRM_Core_PseudoConstant::activityType();
      $this->_activityTypeID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'activity_type_id');
      $defaults['mobilise_type'] = $activityTypes[$this->_activityTypeID];
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
    $mobType = $this->add('select', 'mobilise_type', ts('Mobilisation Type'), $this->getMobilisationTypes(), TRUE);
    if ($this->_id) {
    	$mobType->freeze();
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    if ($this->_id) {
      $activityTypeID = $this->_activityTypeID;
      $sourceRecID    = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'source_record_id');
      $workflow = $sourceRecID ? 'event' : 'activity';

      $activityTypes = CRM_Core_PseudoConstant::activityType();
      $mType = $activityTypes[$activityTypeID];
    } else {
      $values = $this->controller->exportValues($this->_name);
      $mType  = $values['mobilise_type'];
      $workflow = strtolower($this->_metadata[$mType]['type']);

      $activityTypes = array_flip(CRM_Core_PseudoConstant::activityType());
      if (!array_key_exists($mType, $activityTypes)) {
        CRM_Core_Error::fatal(ts("Selected activity type '%1' doesn't exist. Make sure its configured.", array(1 => $mType)));
      }
      $activityTypeID = $activityTypes[$mType];
    }
    $this->controller->set('workflow', $workflow);
    $this->set('activity_type_id', $activityTypeID);
    $this->set('mtype', $mType);
  }

  /**
   * Display Name of the form
   *
   * @access public
   *
   * @return string
   */
  public function getTitle() {
    return ts('Add them to?');
  }
}

