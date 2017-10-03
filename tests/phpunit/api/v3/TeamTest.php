<?php

use CRM_Team_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test class for Team API and it's methods.
 * @package CiviCRM_APIv3
 * @subpackage API_Activity
 * @group headless
 */
class api_v3_TeamTest extends CiviUnitTestCase implements HeadlessInterface {

  public function setUpHeadless() {
    // Comment following block out to setup headdless database.
    /*return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();*/
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Test that a team is not created with empty params.
   */
  public function testCreateWithEmptyParams() {
    $this->callAPIFailure('team', 'create', array());
  }

  /**
   * Test that a team is created with minimum required parameters.
   */
  public function testCreateWithMinimumParams() {
    $teamName = "Agileware Team";
    $params = array(
      "team_name" => $teamName
    );
    $result = $this->callAPISuccess('team', 'create', $params);
    $this->assertEquals($teamName, $result["values"][$result["id"]]["team_name"], 'Check for team name.');
  }

  /**
   * Test that a team is created with all parameters.
   */
  public function testCreateWithAll() {
    $contactId = $this->individualCreate();
    $teamName = "Agileware Team";
    $domainId = CRM_Core_Config::domainID();
    $params = array(
      "team_name"  => $teamName,
      "domain_id"  => $domainId,
      "is_active"  => 0,
      "created_id" => $contactId,
    );

    $result = $this->callAPISuccess('team', 'create', $params);

    $this->assertEquals($teamName, $result["values"][$result["id"]]["team_name"], 'Check for team name.');
    $this->assertEquals($domainId, $result["values"][$result["id"]]["domain_id"], 'Check for domain id.');
    $this->assertEquals($contactId, $result["values"][$result["id"]]["created_id"], 'Check for contact id.');
  }


  /**
   * Test team setting update
   */
  public function testUpdateSettings() {
    $teamName = "Agileware Team";
    $params = array(
      "team_name" => $teamName,
      "is_active"  => 0,
    );
    $result = $this->callAPISuccess('team', 'create', $params);

    $this->assertEquals($teamName, $result["values"][$result["id"]]["team_name"], 'Check for team name.');
    $this->assertEquals(0, $result["values"][$result["id"]]["is_active"], 'Check is_active status.');

    $team_id = $result["id"];

    $teamNameNew = "Agileware Team New";
    $params = array(
      "team_name" => $teamNameNew,
      "id"        => $team_id,
      "is_active"  => 1,
    );

    $result = $this->callAPISuccess('team', 'create', $params);

    $this->assertEquals($teamNameNew, $result["values"][$result["id"]]["team_name"], 'Check for team name updation.');
    $this->assertEquals(1, $result["values"][$result["id"]]["is_active"], 'Check is_active status.');
  }

  /**
   * Test team retrieve by name
   */
  public function testFetchByName() {
    $teamName = "Agileware Team";
    $params = array(
      "team_name" => $teamName,
    );
    $this->callAPISuccess('team', 'create', $params);
    $result = $this->callAPISuccess('team', 'get', $params);
    $this->assertEquals(TRUE, ($result["count"] > 0), 'Check if team found.');
  }

  /**
   * Test team retrieve by contact
   */
  public function testFetchByContact() {
    $contactId = $this->individualCreate();
    $teamName = "Agileware Team";
    $params = array(
      "team_name"  => $teamName,
      "created_id" => $contactId,
    );
    $this->callAPISuccess('team', 'create', $params);

    $params = array(
      "created_id" => $contactId,
    );

    $result = $this->callAPISuccess('team', 'get', $params);
    $this->assertEquals(TRUE, ($result["count"] > 0), 'Check if team found.');
  }

  /**
   * Test deleting a team
   */
  public function testDelete() {
    $teamName = "Agileware Team";
    $params = array(
      "team_name"  => $teamName,
    );
    $team = $this->callAPISuccess('team', 'create', $params);
    $teamid = $team["id"];
    $params = array(
      "id" => $teamid
    );
    $result = $this->callAPISuccess('team', 'delete', $params);
    $this->assertEquals(TRUE, ($result["count"] > 0), 'Check if team found.');
  }
}
