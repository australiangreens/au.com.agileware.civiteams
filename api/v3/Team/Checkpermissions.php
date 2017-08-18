<?php

/**
 * Team.Checkpermission API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_team_Checkpermissions_spec(&$spec) {
  $spec['entity_table'] = [
    'title' => 'Entity Table',
    'description' => 'DAO Table for the Entity permissions should be checked for',
    'api.required' => 1
  ];
  $spec['entity_id'] = [
    'title' => 'Entity ID',
    'description' => 'ID of the Entity permissions should be checked for',
    'api.required' => 1
  ];
  $spec['action'] = [
    'title' => 'Action',
    'description' => 'Action to be performed against the entity, e.g. view, create, edit, delete, publish',
    'api.required' => 1
  ];
  $spec['contact_id'] = [
    'title' => 'Contact ID',
    'description' => 'ID of the contact permissions are being checked for.  Assumes the current user if not set.'
  ];
}

/**
 * Team.Checkpermission API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_team_Checkpermissions($params) {
  $returnValues = CRM_Team_BAO_Team::checkPermissions(
    $params['entity_table'],
    $params['entity_id'],
    $params['action'],
    $params['contact_id']
  );
  return civicrm_api3_create_success($returnValues, $params, 'Team', 'checkPermission');
}

