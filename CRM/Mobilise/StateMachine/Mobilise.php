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
 * State machine for managing different states of the Import process.
 *
 */
class CRM_Mobilise_StateMachine_Mobilise extends CRM_Core_StateMachine {

  /**
   * class constructor
   *
   * @param object  CRM_Mobilise_Controller
   * @param int     $action
   *
   * @return object CRM_Mobilise_StateMachine
   */
  function __construct($controller, $action = CRM_Core_Action::NONE) {
    parent::__construct($controller, $action);

    $this->_pages = array(
      'CRM_Mobilise_Form_Type'    => NULL,
      'CRM_Mobilise_Form_Confirm' => NULL,
    );

    $workflow = $controller->get('workflow');
    if ($controller->get('workflow') == 'activity') {
      $this->_pages = array(
        'CRM_Mobilise_Form_Type'      => NULL,
        'CRM_Mobilise_Form_Activity'  => NULL,
      );
      if ($controller->get('is_new_activity')) {
        $this->_pages['CRM_Mobilise_Form_NewActivity'] = NULL;
      }
      $this->_pages['CRM_Mobilise_Form_Target']  = NULL;
      $this->_pages['CRM_Mobilise_Form_Confirm'] = NULL;

    } else if ($controller->get('workflow') == 'event') {
      $this->_pages = array(
        'CRM_Mobilise_Form_Type'      => NULL,
        'CRM_Mobilise_Form_Event'     => NULL,
      );
      if ($controller->get('is_new_event')) {
        $this->_pages['CRM_Mobilise_Form_NewEvent'] = NULL;
      }
      $this->_pages['CRM_Mobilise_Form_Participant'] = NULL;
      $this->_pages['CRM_Mobilise_Form_Confirm']     = NULL;

    }

    if ($controller->get('ignore_confirm')) {
      unset($this->_pages['CRM_Mobilise_Form_Confirm']);
    }
    $this->addSequentialPages($this->_pages, $action);
  }
}

