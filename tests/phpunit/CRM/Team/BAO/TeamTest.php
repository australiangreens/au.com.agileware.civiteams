<?php

use CRM_Team_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test class for Team BAO and it's methods.
 * @group headless
 */
class CRM_Team_BAO_TeamTest extends CiviUnitTestCase implements HeadlessInterface {

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

  /**
   * Test domain access of teams.
   */
  public function testDomainAccessOfTeams() {
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
    $domainId = $this->createDomain();
    $params["domain_id"] = $domainId;
    $team2 = CRM_Team_BAO_Team::create($params);

    unset($params["domain_id"]);
    $team3 = CRM_Team_BAO_Team::create($params);

    $teams = $this->callAPISuccess("Team","get",array());
    $this->assertEquals(2, $teams["count"], "Should have access to only 2 teams.");
  }

  /**
   * Test team access restriction.
   */
  public function testTeamRestriction() {
    $contactId = $this->individualCreate();
    $teamName = "Agileware Team";
    $domainId = $this->createDomain();
    $params = array(
      "team_name"  => $teamName,
      "domain_id"  => $domainId,
      "is_active"  => 0,
      "created_id" => $contactId,
    );
    $team = CRM_Team_BAO_Team::create($params);
    $this->callAPIFailure("Team","getsingle",array(
      "team_id" => $team->id
    ));
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
     $team = CRM_Team_BAO_Team::create($params);
     $this->assertInstanceOf('CRM_Team_BAO_Team', $team, 'Check for created object');
     $this->assertEquals($teamName, $team->team_name, 'Check for team name.');
     $this->assertEquals(0, $team->is_active, 'Check is_active status.');

     $team_id = $team->id;

     $teamNameNew = "Agileware Team New";
     $params = array(
       "team_name" => $teamNameNew,
       "id"        => $team_id,
       "is_active"  => 1,
     );

     $team = CRM_Team_BAO_Team::create($params);
     $this->assertInstanceOf('CRM_Team_BAO_Team', $team, 'Check for updated object');
     $this->assertEquals($teamNameNew, $team->team_name, 'Check for team name updation.');
     $this->assertEquals(1, $team->is_active, 'Check is_active status.');
   }

   /**
    * Test team retrieve by name
    */
   public function testFetchByName() {
     $teamName = "Agileware Team";
     $params = array(
       "team_name" => $teamName,
     );
     $team = CRM_Team_BAO_Team::create($params);

     $params = array(
       "team_name" => $teamName,
     );
     $team = new CRM_Team_DAO_Team();
     $team->copyValues($params);
     $this->assertEquals(TRUE, $team->find(TRUE), 'Check if team found.');
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
     $team = CRM_Team_BAO_Team::create($params);
     $params = array(
       "created_id" => $contactId,
     );
     $team = new CRM_Team_DAO_Team();
     $team->copyValues($params);
     $this->assertEquals(TRUE, $team->find(), 'Check if team found.');
   }

   /**
    * Test deleting a team
    */
   public function testDelete() {
     $teamName = "Agileware Team";
     $params = array(
       "team_name"  => $teamName,
     );
     $team = CRM_Team_BAO_Team::create($params);
     $teamid = $team->id;

     $team = new CRM_Team_DAO_Team();
     $team->id = $teamid;
     $this->assertEquals(TRUE, $team->delete(), 'Check if team is deleted.');
   }

   /**
    * Create domain to test team access.
    */
   private function createDomain() {
     $result = $this->callAPISuccess('Domain', 'create', array(
       "name"           => "New domain name",
       "domain_version" => "1"
     ));
     return $result["id"];
   }
}
