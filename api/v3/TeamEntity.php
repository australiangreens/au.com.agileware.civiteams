<?php
use CRM_Team_ExtensionUtil as E;

function _civicrm_api3_team_entity_assign_spec(&$spec) {
    setAssignUnassignParams($spec);
}

function _civicrm_api3_team_entity_unassign_spec(&$spec) {
    setAssignUnassignParams($spec);
}

function setAssignUnassignParams(&$spec) {
    $spec['team_id'] = array(
        'api.required' => 1,
        'title' => 'Team ID',
        'type' => CRM_Utils_Type::T_INT,
        'FKClassName' => 'CRM_Team_DAO_Team',
        'FKApiName' => 'Team',
    );

    $spec['entity_id'] = array(
        'api.required' => 1,
        'title' => 'Entity ID',
        'type' => CRM_Utils_Type::T_INT,
    );

    $spec['entity_name'] = array(
        'api.required' => 1,
        'title' => 'Entity Name',
        'type' => CRM_Utils_Type::T_STRING,
    );
}

function _civicrm_api3_team_entity_getmodifiedentities_spec(&$spec) {
    $spec['date_modified'] = array(
        'api.required' => 1,
        'title' => 'Modified Date',
        'type' => CRM_Utils_Type::T_TIMESTAMP,
    );

    $spec['entity_name'] = array(
        'api.required' => 0,
        'title' => 'Entity Name',
        'type' => CRM_Utils_Type::T_STRING,
    );

    $spec['team_id'] = array(
        'api.required' => 0,
        'title' => 'Team ID',
        'type' => CRM_Utils_Type::T_INT,
        'FKClassName' => 'CRM_Team_DAO_Team',
        'FKApiName' => 'Team',
    );
}

/**
 * TeamEntity.assign API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_assign($params) {
    $createparams = array(
        "team_id"     => $params["team_id"],
        "entity_id"   => $params["entity_id"],
        "entity_name" => $params["entity_name"],
        "sequential"  => 1,
    );
    $teamentity = _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $createparams);
    if($teamentity["count"] > 0) {
        if($teamentity["values"][0]["isactive"] == 1) {
            return array(
                "status"  => 0,
                "message" => "Entity is already assigned with the team."
            );
        }
        $createparams["isactive"] = 1;
        $createparams["id"] = $teamentity["id"];
        $createparams["date_modified"] = CRM_Utils_Date::currentDBDate();
    }

    return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $createparams);
}

/**
 * TeamEntity.unassign API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_unassign($params) {
    $getparams = array(
        "team_id"       => $params["team_id"],
        "entity_id"     => $params["entity_id"],
        "entity_name"   => $params["entity_name"],
        "sequential"    => 1,
    );
    $teamentity = _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $getparams);
    if($teamentity["count"] > 0 && $teamentity["values"][0]["isactive"] == 1) {
        $getparams["isactive"] = 0;
        $getparams["id"] = $teamentity["id"];
        $getparams["date_modified"] = CRM_Utils_Date::currentDBDate();
        _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $getparams);
        return array(
          "status"  => 1,
          "message" => "Entity unassigned successfully."
        );
    }

    return array(
        "status"  => 0,
        "message" => "Entity is not assigned with the team."
    );
}

/**
 * TeamEntity.getmodifiedentities API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_getmodifiedentities($params) {
    $getparams = array(
        "date_modified" => array(">=" => $params["date_modified"]),
        "sequential"    => 1,
    );

    if(isset($params["entity_name"])) {
        $getparams["entity_name"] = $params["entity_name"];
    }

    if(isset($params["team_id"])) {
        $getparams["team_id"] = $params["team_id"];
    }

    return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $getparams);
}