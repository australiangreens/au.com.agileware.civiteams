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
  }
}
