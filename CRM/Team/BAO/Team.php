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

    $permissions = array();

    $results = $hook->invoke(
      array('entity_table', 'entity_id', 'action', 'contact_id', 'permissions'),
      $entity_table, $entity_id, $action, $contact_id, $permissions, $_nullObject,
      'civicrm_team_permissions'
    );

    return !(empty($permissions) || in_array(FALSE, $permissions));
  }

  public static function create(&$params) {
    if (empty($params['id'])){
      $session = CRM_Core_Session::singleton();
      $cid = $session->get('userID');

      if($cid) {
        $params['created_id'] = $cid;
      }
    }

    CRM_Core_Error::debug_var("CRM_Team_BAO_Team::create「\$params」({$cid})", $params);

    $team = new CRM_Team_BAO_Team();
    $team->copyValues($params);

    $team->save();

    if (!$team->id) {
      return NULL;
    }

    return $team;
  }
}
