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

    return !in_array(FALSE, $permissions);
  }

  /**
   * Check if we have access to do operations on a team.
   * @param $team
   * @return TRUE|FALSE
   */
  public static function hasTeamAccess($team) {
    if($team["domain_id"] == NULL || $team["domain_id"] == CRM_Core_Config::domainID()) {
      return TRUE;
    }
    return FALSE;
  }

  public static function create(&$params) {
    if(empty($params)){
      return null;
    }

    if (empty($params['id'])){
      $session = CRM_Core_Session::singleton();
      $cid = $session->get('userID');

      if($cid) {
        $params['created_id'] = $cid;
      }
    }

    $team = new CRM_Team_BAO_Team();
    $team->copyValues($params);

    $team->save();

    if (!$team->id) {
      return NULL;
    }

    return $team;
  }

  public function addSelectWhereClause(string $entityName = NULL, int $userId = NULL, array $conditions = []): array {
    $clauses = parent::addSelectWhereClause($entityName, $userId, $conditions);

    if (!CRM_Core_Permission::check('administer civiteams', 'access civiteams')) {
      $contact_id = CRM_Core_Session::getLoggedInContactID();
      $clauses['id'][] = 'IN (SELECT team_id FROM civicrm_team_contact WHERE contact_id = ' . $contact_id . ') ';
    }

    $domain_id = CRM_Core_Config::domainID();
    $clauses['domain_id'][] = '= ' . $domain_id . ' OR domain_id IS NULL';

    return $clauses;
  }
}
