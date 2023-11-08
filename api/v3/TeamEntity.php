<?php
use CRM_Team_ExtensionUtil as E;

function _civicrm_api3_team_entity_create_spec(&$spec) {
    $spec['team_id'] = array(
        'api.required' => 0,
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

    $spec['entity_table'] = array(
        'api.required' => 1,
        'title' => 'Entity Table',
        'type' => CRM_Utils_Type::T_STRING,
    );
}

function _civicrm_api3_team_entity_get_spec(&$spec) {
    $spec['team_id'] = array(
        'api.required' => 0,
        'title' => 'Team ID',
        'type' => CRM_Utils_Type::T_INT,
        'FKClassName' => 'CRM_Team_DAO_Team',
        'FKApiName' => 'Team',
    );

    $spec['entity_id'] = array(
        'api.required' => 0,
        'title' => 'Entity ID',
        'type' => CRM_Utils_Type::T_INT,
    );

    $spec['entity_table'] = array(
        'api.required' => 0,
        'title' => 'Entity Table',
        'type' => CRM_Utils_Type::T_STRING,
    );
}

/**
 * TeamEntity.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_get($params) {
    return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * TeamEntity.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_create($params) {
    return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * TeamEntity.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_team_entity_delete($params) {
    return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}
