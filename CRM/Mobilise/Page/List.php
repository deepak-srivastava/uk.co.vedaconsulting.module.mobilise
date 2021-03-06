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
 * Page for displaying list of mobilisations 
 */
class CRM_Mobilise_Page_List extends CRM_Core_Page {

  /**
   * Get action Links
   *
   * @return array (reference) of action links
   */
  function &links($type) {
    $updateURL  = ($type == 'Event') ? 'civicrm/mobilise/update/event' : 'civicrm/mobilise';
    $updateURLQ = ($type == 'Event') ? 'id=%%id%%&reset=1' : 'action=update&id=%%id%%&reset=1';
    $links = array(
      CRM_Core_Action::UPDATE =>
      array(
        'name' => ts('Edit'),
        'url' => $updateURL,
        'qs' => $updateURLQ,
        'title' => ts('Edit Mobilisation'),
      ),
      CRM_Core_Action::DELETE =>
      array(
        'name' => ts('Delete'),
        'url' => 'civicrm/mobilise/del',
        'qs' => 'action=delete&id=%%id%%',
        'title' => ts('Delete Mobilisation'),
      ),
    );
    return $links;
  }

  function run() {
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/mobilise/list', 'reset=1'));

    $this->browse();
    return parent::run();
  }

  function browse() {
    $mob  = new CRM_Mobilise_Utils_Mobilisation();
    $rows = $mob->getMobilisations();
    foreach ($rows as $key => $value) {
      $action = array_sum(array_keys($this->links($value['type'])));
      $rows[$key]['action'] = CRM_Core_Action::formLink(self::links($value['type']), $action, array('id' => $value['id']));
    }
    $this->assign('rows', $rows);
  }
}
