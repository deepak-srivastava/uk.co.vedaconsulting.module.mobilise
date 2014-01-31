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
    parent::preProcess();

    $this->assign('activity_fields', $this->_metadata[$this->_mtype]['activity_fields']);

    $this->_activityTypeId = $this->get('activity_type_id');
    $activityTypes = CRM_Core_PseudoConstant::activityType(TRUE, FALSE, TRUE);
    if ($actType = CRM_Utils_Array::value($this->_activityTypeId, $activityTypes)) {
      $this->assign('activityType', $actType);
    } else {
      CRM_Core_Error::fatal("Can't determine activity type.");
    }
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

    list($defaults['activity_date_time'], $defaults['activity_date_time_time']) = 
      CRM_Utils_Date::setDateDefaults(NULL, 'activityDateTime');
    if ($this->_id) {
      // lets not use Activity::retrieve method as that would also retrieve assignee/target details, we not interested in
      $activity = new CRM_Activity_DAO_Activity();
      $activity->id = $this->_id;
      if ($activity->find(TRUE)) {
        CRM_Core_DAO::storeValues($activity, $defaults);
        list($defaults['activity_date_time'], $defaults['activity_date_time_time']) = 
          CRM_Utils_Date::setDateDefaults($defaults['activity_date_time'], 'activityDateTime');
      }
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
    if (in_array('activity_date', $this->_metadata[$this->_mtype]['activity_fields'])) {
      if (array_key_exists('activity_end_date', $this->_metadata[$this->_mtype]['activity_fields'])) {
        // don't add time if date range is expected
        $this->addDate('activity_date_time', ts('Date From'), TRUE, array('formatType' => 'activityDate'));
      } else {
        $this->addDateTime('activity_date_time', ts('Date'), TRUE, array('formatType' => 'activityDateTime'));
      }
    }
    if (in_array('notes', $this->_metadata[$this->_mtype]['activity_fields'])) {
      $this->add('textarea', 'details', ts('Notes'), 'rows=3, cols=60', FALSE);
    }

    // custom handling
    if (array_key_exists('custom', $this->_metadata[$this->_mtype]['activity_fields'])) {
      $this->set('type', 'Activity');
      $this->set('subType',  $this->_activityTypeId);
      $this->set('entityId', $this->_id);
      $this->set('cgcount',  1);
      CRM_Custom_Form_CustomData::preProcess($this);
      foreach ($this->_groupTree as $gID => &$grpVals) {
        foreach ($grpVals['fields'] as $fID => &$fldVals) {
          if (!in_array($fldVals['label'], $this->_metadata[$this->_mtype]['activity_fields']['custom'])) {
            unset($grpVals['fields'][$fID]);
          } else if (strtolower($fldVals['label']) == "amount") {
            $this->assign("amountCustomIdPrefix", "custom_{$fID}_");
          }
          if (array_key_exists('activity_end_date', $this->_metadata[$this->_mtype]['activity_fields'])) {
            if ($fldVals['label'] == $this->_metadata[$this->_mtype]['activity_fields']['activity_end_date']) {
              $dateCustomFieldID = $fID;
              $dateCustomFieldLabel = $fldVals['label'];
            } 
          }
        }
      }
      CRM_Custom_Form_CustomData::buildQuickForm($this);
    }

    // add custom end date if configured in meta data
    if (array_key_exists('custom', $this->_metadata[$this->_mtype]['activity_fields']) &&
      array_key_exists('activity_end_date', $this->_metadata[$this->_mtype]['activity_fields'])) {
      $this->_dateCustomFieldName = "custom_{$dateCustomFieldID}_-1";
      $this->assign('customDate', $this->_dateCustomFieldName);
      $this->addDate($this->_dateCustomFieldName, $dateCustomFieldLabel, TRUE, array('formatType' => 'activityDate'));
    }
    parent::buildQuickForm();

    if (array_key_exists('activity_end_date', $this->_metadata[$this->_mtype]['activity_fields'])) {
      $this->addFormRule(array('CRM_Mobilise_Form_Target', 'formRule'), $this);
    }
  }

  /**
   * global form rule
   *
   * @param array $fields  the input form values
   * @param array $files   the uploaded files if any
   * @param array $options additional user data
   *
   * @return true if no errors, else array of errors
   * @access public
   * @static
   */
  static function formRule($fields, $files, $self) {
    $errors = array();

    $fromDate = CRM_Utils_Date::processDate(CRM_Utils_Array::value('activity_date_time', $fields));
    $endDate  = CRM_Utils_Date::processDate(CRM_Utils_Array::value($self->_dateCustomFieldName, $fields));
    if ($endDate < $fromDate) {
      $errors[$self->_dateCustomFieldName] = ts("To date can't be earlier than the From Date.");
    }

    return $errors;
  }

  public function postProcess() {
    $params  = $this->controller->exportValues($this->_name);

    $params['id']                  = $this->_id;
    $params['status_id']           = CRM_Core_OptionGroup::getValue('activity_status', 'Completed', 'name');
    $params['activity_date_time']  = CRM_Utils_Date::processDate(
      $params['activity_date_time'], $params['activity_date_time_time']);

    if (!$this->_id) {
      $params['source_contact_id']   = $this->_schoolId;
      $params['assignee_contact_id'] = array($this->_currentUserId);
      $params['activity_type_id']    = $this->_activityTypeId;
    } else {
      // make sure we not deleting assignees or targets for update action
      $params['deleteActivityAssignment'] = FALSE;
      $params['deleteActivityTarget']     = FALSE;
    }

    // custom params handling
    if (array_key_exists('custom', $this->_metadata[$this->_mtype]['activity_fields'])) {
      $customFields = CRM_Core_BAO_CustomField::getFields('Activity', FALSE, FALSE,$this->_activityTypeId);
      $customFields = CRM_Utils_Array::crmArrayMerge(
        $customFields,
        CRM_Core_BAO_CustomField::getFields('Activity', FALSE, FALSE,NULL, NULL, TRUE));
      
      // format custom params
      $params['custom'] = 
        CRM_Core_BAO_CustomField::postProcess($params,
          $customFields,
          $this->_id,
          'Activity');
    }

    if (!$this->_id) {
      $targetContactIDs = array();
      foreach ($this->get('cids') as $cid) {
        if (CRM_Utils_Type::validate($cid, 'Integer')) {
          $targetContactIDs[] = $cid;
        }
      }
      $params['target_contact_id'] = $targetContactIDs;
    }

    $count    = 0;
    $activity = CRM_Activity_BAO_Activity::create($params);
    if ($activity->id) {
      $count++;
    }

    if ($count > 0) {
      $statusMsg = ts('Mobilisation successfully created for the selected alumni.');
    } else {
      $statusMsg = ts('Could not create any mobilisations.');
    }
    $this->set('status', $statusMsg);
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
