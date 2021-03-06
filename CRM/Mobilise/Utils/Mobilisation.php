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
class CRM_Mobilise_Utils_Mobilisation {

  protected $_metadata = array();

  function __construct() {
    // initialize metadata
    $mob = new CRM_Mobilise_Form_Mobilise();
    $this->_metadata = $mob->getVar('_metadata');

    require_once 'CRM/Futurefirst/veda_FF_utils.php';
    $this->_schoolId = CRM_Futurefirst_veda_FF_utils::get_teacher_school_ID();
    if (!$this->_schoolId) {
      CRM_Core_Error::fatal(ts("Can't find the school contact."));
    }
  }

  function getMobilisations($contactID = NULL, $limit = NULL, $isBuildAlumniList = TRUE) {
    $mobilisations    = array();
    $mobActivityTypes = array_keys($this->_metadata);

    $eventCustomInfo    = $this->getCustomInfo(CRM_Mobilise_Form_Mobilise::SCHOOL_CUSTOM_SET_TITLE);
    $activityCustomInfo = $this->getCustomInfo(CRM_Mobilise_Form_Mobilise::ACTIVITY_CUSTOM_SET_TITLE);

    $where   = array();
    $where[] = "cov.label IN ('" . implode("', '", $mobActivityTypes) . "')";
    $where[] = "ca.source_contact_id = %1";
    $params  = array(1 => array($this->_schoolId, 'Integer'));
    if ($contactID) {
      $where[]   = "cat.target_contact_id = %2";
      $params[2] = array($contactID, 'Integer');
    }
    $whereClause = "WHERE " . implode(" AND ", $where);

    $limitCLause = "";
    if ($limit) {
      $limitCLause = "LIMIT " . (int)$limit;
    }
    $query = "
    SELECT ca.id as mobID,
           cov.label as mobilisation,
           IF(ce.id IS NULL, ca.activity_date_time, ce.start_date) as date,
           IF(ce.id IS NULL, ca.activity_date_time, ce.end_date) as end_date,
           ca.details,
           ce.id as event_id,
           ce.title,
           eventc.*,
           actc.*
      FROM civicrm_activity ca";
    if ($contactID) {
      $query .= "
INNER JOIN civicrm_activity_target cat              ON cat.activity_id = ca.id";
    }
    $query .= "
INNER JOIN civicrm_option_value cov                 ON ca.activity_type_id = cov.value
INNER JOIN civicrm_option_group cog                 ON cog.id = cov.option_group_id AND cog.name = 'activity_type'
 LEFT JOIN civicrm_event ce                         ON ca.source_record_id = ce.id AND ce.is_active = 1
 LEFT JOIN {$eventCustomInfo['table_name']} eventc  ON ce.id = eventc.entity_id
 LEFT JOIN {$activityCustomInfo['table_name']} actc ON ca.id = actc.entity_id
{$whereClause}
ORDER BY ca.activity_date_time DESC
{$limitCLause}";
    $mob = CRM_Core_DAO::executeQuery($query, $params);
    while ($mob->fetch()) {
      $mobilisations[$mob->mobID] = array();
      $mobilisations[$mob->mobID]['id']           = $mob->mobID;
      $mobilisations[$mob->mobID]['mobilisation'] = $mob->mobilisation;
      $mobilisations[$mob->mobID]['type']         = $this->_metadata[$mob->mobilisation]['type'];
      $mobilisations[$mob->mobID]['event_id']     = $mob->event_id;
      $mobilisations[$mob->mobID]['title']        = $mob->title;
      $mobilisations[$mob->mobID]['date']         = $mob->date;
      $mobilisations[$mob->mobID]['end_date']     = $mob->end_date;

      if ($this->_metadata[$mob->mobilisation]['type'] == 'Event') {
        $mobilisations[$mob->mobID]['staff'] = 
          $mob->{$eventCustomInfo[CRM_Mobilise_Form_Mobilise::SCHOOL_STAFF_CUSTOM_FIELD_TITLE]['column_name']};
        $mobilisations[$mob->mobID]['session'] = 
          $mob->{$eventCustomInfo[CRM_Mobilise_Form_Mobilise::SCHOOL_SESSION_CUSTOM_FIELD_TITLE]['column_name']};
        $mobilisations[$mob->mobID]['notes'] = 
          $mob->{$eventCustomInfo[CRM_Mobilise_Form_Mobilise::SCHOOL_NOTE_CUSTOM_FIELD_TITLE]['column_name']};
        if (!$contactID && $isBuildAlumniList) {
          $mobilisations[$mob->mobID]['alumni'] = 
            $this->getEventAlumni($mob->mobID, $mob->mobilisation);
          $mobilisations[$mob->mobID]['student'] = 
            $this->getEventAlumni($mob->mobID, $mob->mobilisation, TRUE);
        }
      }
      if ($this->_metadata[$mob->mobilisation]['type'] == 'Activity') {
        $mobilisations[$mob->mobID]['amount']  = 
          $mob->{$activityCustomInfo[CRM_Mobilise_Form_Mobilise::ACTIVITY_AMOUNT_CUSTOM_FIELD_TITLE]['column_name']};
        $mobilisations[$mob->mobID]['purpose'] = 
          $mob->{$activityCustomInfo[CRM_Mobilise_Form_Mobilise::ACTIVITY_PURPOSE_CUSTOM_FIELD_TITLE]['column_name']};
        $mobilisations[$mob->mobID]['end_date']  = 
          $mob->{$activityCustomInfo[CRM_Mobilise_Form_Mobilise::ACTIVITY_TODATE_CUSTOM_FIELD_TITLE]['column_name']};
        if (!$contactID && $isBuildAlumniList) {
          $mobilisations[$mob->mobID]['alumni']  = 
            $this->getActivityAlumni($mob->mobID);
        }
        $mobilisations[$mob->mobID]['notes']  = $mob->details;
      }
    }
    return $mobilisations;
  }

  function hasAccessToMobilisationID($mobID) {
    if (!$mobID) return FALSE;

    $mobilisations = $this->getMobilisations(NULL, NULL, FALSE);
    return in_array($mobID, array_keys($mobilisations));
  }

  function getActivityAlumni($activityID) {
    $targetContacts = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId($activityID);
    $aActivityAlumni = array();
    if (!empty($targetContacts)) {
      $query = "
        SELECT CONCAT(first_name, ' ', last_name) as alumni,
        cc.id as cid
        FROM civicrm_contact cc
        WHERE cc.id IN (" . implode(", ", $targetContacts) . ")";
      $dao = CRM_Core_DAO::executeQuery($query);
      while($dao->fetch()){
        $url = CRM_Utils_System::url('school-dashboard/alumni/view', "reset=1&gid=14&id={$dao->cid}");
        $aActivityAlumni['alumni'][] = sprintf("<a href='{$url}'>%s</a>", $dao->alumni);
      }
      return is_array($aActivityAlumni['alumni']) ? 
      implode(', ', $aActivityAlumni['alumni']) : '';
    }
    return NULL;
  }
 
  function getEventAlumni($activityID, $mType, $isStudentRole = FALSE) {
    static $eventAlumni = array();
    if ($isStudentRole) {
      if (CRM_Utils_Array::value('student_contact', $this->_metadata[$mType]['participant_fields'])) {
        $alumniRoles = $this->_metadata[$mType]['participant_fields']['student_contact'];
      } else { 
        return NULL;
      }
    }
    $cacheKey = "key_" . (empty($alumniRoles) ? "" : implode("_", $alumniRoles));

    if (!array_key_exists($cacheKey, $eventAlumni)) {
      $eventAlumni[$cacheKey] = array();
      $mobActivityTypes = array_keys($this->_metadata);

      $roleClause    = "";
      $alumniRoleIDs = array();
      if (!empty($alumniRoles)) {
        $rolesList = array_flip(CRM_Event_PseudoConstant::participantRole());
        foreach ($alumniRoles as $role) {
          if ($roleID = CRM_Utils_Array::value($role, $rolesList)) {
            $alumniRoleIDs[] = $roleID;
          }
        }
        if (!empty($alumniRoleIDs)) {
          $roleClause = "AND cp.role_id REGEXP '[[:<:]]" . implode('|', $alumniRoleIDs) . "[[:>:]]'";
        }
      }

      $query = "
        SELECT ca.id as mobID,
        CONCAT(cc.first_name, ' ', cc.last_name) as alumni,
        cc.id as cid
        FROM civicrm_activity ca
        INNER JOIN civicrm_activity_target cat              ON cat.activity_id = ca.id
        INNER JOIN civicrm_option_value cov                 ON ca.activity_type_id = cov.value
        INNER JOIN civicrm_option_group cog                 ON cog.id = cov.option_group_id AND cog.name = 'activity_type'
        INNER JOIN civicrm_event ce                         ON ca.source_record_id = ce.id AND ce.is_active = 1
        INNER JOIN civicrm_participant cp                   ON cat.target_contact_id = cp.contact_id AND ce.id = cp.event_id
        INNER JOIN civicrm_contact     cc                   ON cc.id = cp.contact_id
        WHERE cov.label IN ('" . implode("', '", $mobActivityTypes) . "') {$roleClause}
        ORDER BY cc.first_name ASC";
      $dao  = CRM_Core_DAO::executeQuery($query);
      $eventAlumni[$cacheKey][$dao->mobID]['alumni'] = array();
      while ($dao->fetch()) {
        $url = CRM_Utils_System::url('school-dashboard/alumni/view', "reset=1&gid=14&id={$dao->cid}");
        $eventAlumni[$cacheKey][$dao->mobID]['alumni'][] = sprintf("<a href='{$url}'>%s</a>", $dao->alumni);
      }
    }
    return is_array($eventAlumni[$cacheKey][$activityID]['alumni']) ? 
      implode(', ', $eventAlumni[$cacheKey][$activityID]['alumni']) : '';
  }

  function getCustomInfo($title) {
    $sql = "
      SELECT     g.table_name, f.name, f.column_name, f.label as title
      FROM       civicrm_custom_field f
      INNER JOIN civicrm_custom_group g ON f.custom_group_id = g.id
      WHERE      ( g.title = %1 )
      ";
    $params = array(1 => array($title, 'String'));
    $dao    = CRM_Core_DAO::executeQuery($sql, $params);
    while ($dao->fetch()) {
      $customInfo['table_name'] = $dao->table_name;
      $customInfo[$dao->title]   = 
        array('column_name' => $dao->column_name, 
        'title' => $dao->title, 
        'name'  => $dao->name,);
    }
    return $customInfo;
  }

}
