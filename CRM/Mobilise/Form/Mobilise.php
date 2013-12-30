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

  protected $_metadata = 
    array(
      'careers' => array( 
        'type'  => 'Event',
        'title' => 'Careers', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'mentor' => array( 
        'type'  => 'Event',
        'title' => 'Mentor', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'work_exp' => array( 
        'type'  => 'Event',
        'title' => 'Work Experience', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'fundraising' => array( 
        'type'  => 'Activity',
        'title' => 'Donations / Fundraising', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'governor' => array( 
        'type'  => 'Activity',
        'title' => 'Governor', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'non_careers' => array( 
        'type'  => 'Event',
        'title' => 'Non-Careers', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
      'others' => array( 
        'type'  => 'Activity',
        'title' => 'Others', 
        'event_fields' => array(
          'type',
          'name',
          'start_date',
          'end_date'
        ),
      ),
    );

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $session = CRM_Core_Session::singleton();
    //FIXME: report instance id is hardcoded
    $session->pushUserContext(CRM_Utils_System::url('civicrm/report/instance/41', 'force=1'));
  }

  function getMobiliseTypes() {
    $mobTypes = array();
    foreach ($this->_metadata as $key => $vals) {
      $mobTypes[$key] = "{$vals['title']} ({$vals['type']})";
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

