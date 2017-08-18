<?php

class CRM_Team_BAO_Team extends CRM_Team_DAO_Team {
  public static function checkPermissions($entity_table, $entity_id, $action = 'view', $contact_id = NULL) {
    if ($contact_id == NULL) {
      if(CRM_Core_Permission::check('administer CiviCRM')) {
        return TRUE;
      }
      $contact_id = CRM_Core_Session::singleton()->getLoggedInContactID();
    }

    $hook = CRM_Utils_Hook::singleton();

    $_nullObject =& CRM_Utils_Hook::$_nullObject;

    $results = $hook->invoke(
      array('entity_table', 'entity_id', 'action', 'contact_id'),
      $entity_table, $entity_id, $action, $contact_id,
      $_nullObject, $_nullObject,
      'civicrm_team_permissions'
    );

    CRM_Core_Error::debug_var('CRM_Team_BAO_Team::checkPermissions()::「results」', $results);

    return +( is_array($results) ? !in_array(FALSE, $results) : !!$results );
  }
}
