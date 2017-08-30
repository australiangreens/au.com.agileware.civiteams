<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_Teams extends CRM_Core_Form {
  protected $columns;
  protected $select;

  private $limit = 10;
  private $offset = 0;

  public function buildQuickForm() {
    // Team Name Form Entry.
    $this->add('text', 'team_name', ts('Team Name'));
    $this->add('text', 'member_name', ts('Member Name or Email'));

    $this->addCheckBox('status', ts('Status'),
      array(ts('Enabled') => 'enabled', ts('Disabled') => 'disabled'),
      NULL, NULL, NULL, NULL, "\n", FALSE
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('searchElements', ['team_name','member_name','status']);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);

    $parent = $this->controller->getParent();

    $selector = $parent->selector();

    if(!empty($params['team_name'])) {
      $selector->where('t.team_name like @name', array('name' => "%{$params['team_name']}%"));
    }

    if (!empty($params['member_name'])) {
      $c = CRM_Contact_BAO_Contact::getTableName();
      $e = CRM_Core_BAO_Email::getTableName();
      $selector->join('c', "INNER JOIN `$c` c ON c.id = tc.contact_id"); // Pull in the Contact table
      $selector->join('e', "INNER JOIN (SELECT contact_id, GROUP_CONCAT(DISTINCT email, ', ') emails FROM `$e` GROUP BY contact_id) e ON e.contact_id = c.id"); // Having pulled in the Contact table, we need emails too.
      $selector->where(
        '(c.sort_name LIKE @name OR c.display_name LIKE @name OR e.emails LIKE @name)',
        array('name' => "%{$params['member_name']}%")
      );

      parent::postProcess();
    }

    if(!empty($params['status'])) {
      $status = array();
      if(!empty($params['status']['enabled'])) {
        $status[] = 1;
      }
      if(!empty($params['status']['disabled'])) {
        $status[] = 0;
      }
      $selector->where('t.is_active IN (@status)', array('status' => $status));
    }
  }
}
