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
 * Implements hook_civicrm_entityTypes().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function civiteams_civicrm_entityTypes(&$entityTypes) {
  _civiteams_civix_civicrm_entityTypes($entityTypes);
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

 // */

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

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function civiteams_civicrm_postInstall() {
  _civiteams_civix_civicrm_postInstall();
}

// /**
//  * Implements hook_civicrm_entityTypes().
//  *
//  * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
//  */
// function civiteams_civicrm_entityTypes(&$entityTypes) {
//   _civiteams_civix_civicrm_entityTypes($entityTypes);
// }
