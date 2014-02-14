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
class CRM_Mobilise_Form_Delete extends CRM_Core_Form {

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $this->_cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
    $activityTypeID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'activity_type_id');
    $activityTypes  = CRM_Core_PseudoConstant::activityType();

    if ($mType = CRM_Utils_Array::value($activityTypeID, $activityTypes)) {
      $mob = new CRM_Mobilise_Form_Mobilise();
      $metadata = $mob->getVar('_metadata');
      if (!array_key_exists($mType, $metadata)) {
        CRM_Core_Error::statusBounce(ts("Doesn't look like a mobilisation."));
      }
    } else {
      CRM_Core_Error::statusBounce(ts("Doesn't look like a mobilisation."));
    }

    if ($this->_cid) {
      $confirmMsg = "Are you sure you want to delete this Participation?";
    } else {
      $confirmMsg = "Are you sure you want to delete this {$mType} Mobilisation?";
    }
    $this->assign('confirmMsg', $confirmMsg);
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
        'name' => ts('Delete'),
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
    $sourceRecID = CRM_Core_DAO::getFieldValue('CRM_Activity_DAO_Activity', $this->_id, 'source_record_id');
    if ($sourceRecID) {
      // delete any participants that are part of mobilisation
      $query = "
            SELECT cp.id
              FROM civicrm_activity ca
        INNER JOIN civicrm_activity_target cat              ON cat.activity_id = ca.id
        INNER JOIN civicrm_event ce                         ON ca.source_record_id = ce.id
        INNER JOIN civicrm_participant cp                   ON cat.target_contact_id = cp.contact_id AND ce.id = cp.event_id
             WHERE ca.id = %1";
      $params = array(1 => array($this->_id, 'Integer'));

      if ($this->_cid) {
        $query .= " AND cp.contact_id = %2";
        $params[2] = array($this->_cid, 'Integer');
      }
      $dao = CRM_Core_DAO::executeQuery($query, $params);
      while ($dao->fetch()) {
        CRM_Event_BAO_Participant::deleteParticipant($dao->id);
      }
    }

    if ($this->_cid) {
      $target = new CRM_Activity_BAO_ActivityTarget();
      $target->activity_id = $this->_id;
      $target->target_contact_id = $this->_cid;
      $result = $target->delete();
      $status = ts("Participation Deleted.");
    } else {
      $activityParams = array('id' => $this->_id);
      $result = CRM_Activity_BAO_Activity::deleteActivity($activityParams);
      $status = ts("Mobilisation Deleted.");
    }
    if ($status) {
      CRM_Core_Session::setStatus($status);
    }
  }
}

