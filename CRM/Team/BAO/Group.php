<?php

class CRM_Team_BAO_Group extends CRM_Team_DAO_Group {

  /**
   * Create a new Group based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Team_DAO_Group|NULL
   *
  public static function create($params) {
    $className = 'CRM_Team_DAO_Group';
    $entityName = 'Group';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */
}
