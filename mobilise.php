<?php

require_once 'mobilise.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function mobilise_civicrm_config(&$config) {
  _mobilise_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function mobilise_civicrm_xmlMenu(&$files) {
  _mobilise_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function mobilise_civicrm_install() {
  return _mobilise_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function mobilise_civicrm_uninstall() {
  return _mobilise_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function mobilise_civicrm_enable() {
  return _mobilise_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function mobilise_civicrm_disable() {
  return _mobilise_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function mobilise_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mobilise_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function mobilise_civicrm_managed(&$entities) {
  return _mobilise_civix_civicrm_managed($entities);
}

function mobilise_civicrm_contactListQuery(&$query, $name, $context, $id) {
  if ($context == 'mobiliseGetStudentList') {
    require_once 'CRM/Futurefirst/veda_FF_utils.php';
    $schoolId = CRM_Futurefirst_veda_FF_utils::get_teacher_school_ID();
    if (!$schoolId) {
      CRM_Core_Error::fatal(ts("Can't find the school contact."));
    }

    $relTypeId = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_RelationshipType', 'School is', 'id', 'name_a_b');
    $query = "
    SELECT cc.id, cc.sort_name, CONCAT_WS(' :: ', cc.sort_name, eml.email) as data
      FROM civicrm_contact cc 
 LEFT JOIN civicrm_email eml ON ( cc.id = eml.contact_id AND eml.is_primary = 1 )
INNER JOIN civicrm_relationship rel ON rel.contact_id_a = cc.id AND rel.contact_id_b = {$schoolId} AND rel.relationship_type_id = {$relTypeId}
     WHERE cc.sort_name LIKE '$name%' AND cc.is_deleted = 0
  ORDER BY cc.sort_name 
  LIMIT 0, 10";
  }
}
