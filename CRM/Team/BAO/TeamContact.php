<?php

class CRM_Team_BAO_TeamContact extends CRM_Team_DAO_TeamContact {

  /**
   * Create a new Team Contact based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Team_DAO_Contact|NULL
   *
   */

  public static function create($params) {
    if(empty($params) || empty($params["contact_id"]) || empty($params["team_id"]) || empty($params["status"])) {
      return NULL;
    }

    $entityName = "CRM_Team_BAO_TeamContact";
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $teamContact = new CRM_Team_BAO_TeamContact();
    $teamContact->copyValues($params);
    $teamContact->save();
    if (!$teamContact->id) {
      return NULL;
    }
    CRM_Utils_Hook::post($hook, $entityName, $teamContact->id, $teamContact);
    return $teamContact;
  }
}
