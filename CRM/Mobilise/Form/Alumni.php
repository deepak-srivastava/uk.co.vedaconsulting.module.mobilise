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
class CRM_Mobilise_Form_Alumni extends CRM_Core_Form {

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
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $roleTypes = array();
    $roleids   = CRM_Event_PseudoConstant::participantRole();
    foreach ($roleids as $rolekey => $rolevalue) {
      $roleTypes[] = $this->createElement('checkbox', $rolekey, NULL, $rolevalue,
        array('onclick' => "showCustomData( 'Participant', {$rolekey}, {$this->_roleCustomDataTypeID} );")
      );
    }
    $this->addGroup($roleTypes, 'role_id', ts('Participant Role'));
    $this->addRule('role_id', ts('Role is required'), 'required');

    $this->addDateTime('register_date', ts('Registration Date'), TRUE, array('formatType' => 'activityDateTime'));

    $status = CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label');
    $this->add('select', 'status_id', ts('Participant Status'), 
      array('' => ts('- select -')) + $status, TRUE);

    $this->add('text', 'source', ts('Event Source'));

    $buttons = array(
      array('type' => 'next',
        'name' => ts('Save and Done'),
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

