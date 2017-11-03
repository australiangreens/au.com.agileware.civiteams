<?php

/**
 * Team.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_team_create_spec(&$spec) {
   $spec['team_name']['api.required'] = 1;
}

/**
 * Team.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_create($params) {
  if(isset($params['data']) && (is_array($params['data']) || is_object($params['data']))) {
    $params['data'] = json_encode($params['data']);
  }
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Team.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Team.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_get($params) {
  if(isset($params["return"])) {
    if(is_array($params["return"])) {
      $params["return"][] = "domain_id";
    } else {
      $currentParam = $params["return"];
      $params["return"] = array($currentParam, "domain_id");
    }
  }
  $domainID = CRM_Core_Config::domainID();
  $teams = _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
  $returnTeams = $teams;
  $returnTeams["values"] = array();
  if(is_array($teams['values'])) {
    foreach($teams['values'] as $id => $team) {
      if(!empty($team['data'])) {
        $team['data'] = json_decode($team['data'], TRUE);
      }

      if(!isset($team["domain_id"]) || $team["domain_id"] == $domainID) {
        $returnTeams["values"][] = $team;
      }
    }
  }
  $returnTeams["count"] = count($returnTeams["values"]);
  return $returnTeams;
}
