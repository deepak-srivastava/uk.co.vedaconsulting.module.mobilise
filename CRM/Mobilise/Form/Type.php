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
    $cids = CRM_Utils_Array::value('cids', $_REQUEST);
    if (empty($cids)) {
      $cids = $this->get('cids');
    } else {
      $this->set('cids', $cids);
    }

    if (empty($cids)) {
      CRM_Core_Error::fatal(ts("Could not find valid contact ids"));
    }
    parent::preProcess();
  }

  /**
   * Function to actually build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    $this->add('select', 'mobilise_type', ts('Mobilisation'), $this->getMobiliseTypes(), TRUE);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->controller->exportValues($this->_name);
    $this->set('mtype', $values['mobilise_type']);
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

