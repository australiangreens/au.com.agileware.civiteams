<?php

use CRM_Team_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_Team_BAO_TeamTest extends CiviUnitTestCase implements HeadlessInterface {

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
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
   * Test that a team is not created with empty array.
   */
  public function testCreateWithEmptyParams() {
    $params = array();
    $team = CRM_Team_BAO_Team::create($params);
    $this->assertNull($team);
  }

  /**
   * Test that a team is created with minimum required parameters.
   */
  public function testCreateWithMinimumParams() {
    $teamName = "Agileware Team";
    $params = array(
      "team_name" => $teamName
    );
    $team = CRM_Team_BAO_Team::create($params);
    $this->assertInstanceOf('CRM_Team_BAO_Team', $team, 'Check for created object');
    $this->assertEquals($teamName, $team->team_name, 'Check for team name.');

    $team_id = $team->id;

    $teamNameNew = "Agileware Team New";
    $params = array(
      "team_name" => $teamNameNew,
      "id"        => $team_id,
    );

    $team = CRM_Team_BAO_Team::create($params);
    $this->assertInstanceOf('CRM_Team_BAO_Team', $team, 'Check for updated object');
    $this->assertEquals($teamNameNew, $team->team_name, 'Check for team name updation.');
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

    $team = CRM_Team_BAO_Team::create($params);
    $this->assertInstanceOf('CRM_Team_BAO_Team', $team, 'Check for created object');
    $this->assertEquals($teamName, $team->team_name, 'Check for team name.');
    $this->assertEquals($domainId, $team->domain_id, 'Check for domain id.');
    $this->assertEquals($contactId, $team->created_id, 'Check for contact id.');
  }
}
