<?php

require_once 'civiteams.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civiteams_civicrm_config(&$config) {
  _civiteams_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civiteams_civicrm_xmlMenu(&$files) {
  _civiteams_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civiteams_civicrm_install() {
  _civiteams_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civiteams_civicrm_uninstall() {
  _civiteams_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civiteams_civicrm_enable() {
  _civiteams_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civiteams_civicrm_disable() {
  _civiteams_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civiteams_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civiteams_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civiteams_civicrm_managed(&$entities) {
  _civiteams_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civiteams_civicrm_caseTypes(&$caseTypes) {
  _civiteams_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civiteams_civicrm_angularModules(&$angularModules) {
_civiteams_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civiteams_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civiteams_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function civiteams_civicrm_entityTypes(&$entityTypes) {
  $entityFiles = _civiteams_civix_find_files(__DIR__, '*.entityType.php');
  foreach ($entityFiles as $file) {
    $et = include $file;
    foreach ($et as $e) {
      $entityTypes[$e['class']] = $e;
    }
  }
}

/**
 * Implements hook_civicrm_searchTasks().
 */
function civiteams_civicrm_searchTasks($objectType, &$tasks) {
  if($objectType == 'contact') {
    $tasks[] = array (
      'title' => ts('Team - add contacts'),
      'class' => 'CRM_Team_Form_AddContacts',
    );
    $tasks[] = array (
      'title' => ts('Team - remove contacts'),
      'class' => 'CRM_Team_Form_RemoveContacts',
    );
  }
}

function civiteams_civicrm_permission(&$permissions) {
  $permissions['access civiteams']     = ts('CiviTeams') . ': ' . ts('Access Team Listing');
  $permissions['administer civiteams'] = ts('CiviTeams') . ': ' . ts('Administer Teams');
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function civiteams_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function civiteams_civicrm_navigationMenu(&$menu) {
  _civiteams_civix_insert_navigation_menu($menu, 'Contacts', array(
    'label' => ts('Manage Teams', array('domain' => 'au.com.agileware.civiteams')),
    'name' => 'manage_teams',
    'url' => 'civicrm/teams',
    'permission' => 'access civiteams,administer civiteams',
    'operator' => 'OR',
    'separator' => 2,
  ));
  _civiteams_civix_insert_navigation_menu($menu, 'Contacts', array(
    'label' => ts('Add Team', array('domain' => 'au.com.agileware.civiteams')),
    'name' => 'add_team',
    'url' => 'civicrm/teams/settings?action=add',
    'permission' => 'administer civiteams',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _civiteams_civix_navigationMenu($menu);
} // */

function civiteams_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['team']['get'] = array ('or' => array('access civiteams', 'administer civiteams'));
}