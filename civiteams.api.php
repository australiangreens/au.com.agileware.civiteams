<?php

/**
 * Used by CRM_Team_BAO_Team::checkPermissions() to determine if a contact can
 * perform an operation on a given entity table.
 *
 * Users with 'administer CiviCRM' UF permissions shortcut this hook.
 * After all implementations are run, `$permissions` must contain only true values
 * for `CRM_Team_BAO_Team::checkPermissions()` to succeed.
 *
 * @param $entity_table        Table permissions are being checked for
 *                             e.g. civicrm_mailing.
 * @param $entity_id           ID being checked for in $entity_table.
 * @param $action              Action being performed, e.g. 'view', 'edit', 'create'.
 * @param $contact_id          ID of the contact needing permissions to act.
 * @param array &$permissions  List of permissions to append to.
 */
function hook_civicrm_team_permissions($entity_table, $entity_id, $action, $contact_id, &$permissions){
  if ($entity_table = 'civicrm_myentity') {
    $teams = civicrm_api3('TeamContact', 'get', array(
      'contact_id' => $contact_id,
      'options' => array( 'limit' => 0 )
    ));

    $team_ids = array_keys($teams);

    switch($action) {
    case 'view':
      $permissions[] = !empty(array_intersect($team_ids, myentity_teams_view($entity_id)));
      break;
    case 'edit':
    case 'update':
      $permissions[] = !empty(array_intersect($team_ids, myentity_teams_edit($entity_id)));
      break;
    case 'delete':
      $permissions[] = !empty(array_intersect($team_ids, myentity_teams_delete($entity_id)));
      break;
    default:
      $permissions[] = FALSE;
    }
  }
}
