<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_TeamContacts extends CRM_Core_Form {
  protected $columns;
  protected $select;

  private $limit = 10;
  private $offset = 0;

  private $searchEls = array('member_name');

  public function buildQuickForm() {
    // Team Name Form Entry.5f
    $this->add('text', 'member_name', ts('Name or Email'));

    $this->addButtons(array(
      array(
        'type' => 'refresh',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));

    $this->add('hidden', 'team_id');

    // export form elements
    $this->assign('searchElements', $this->searchEls);

    $defaults = array();

    if($team_id = CRM_Utils_Request::retrieve('team_id' , 'Integer')) {
      $defaults['team_id'] = $team_id;
    }

    $this->setDefaults($defaults);

    parent::buildQuickForm();
  }

  function addSearchElement($name) {
    if (!in_array($name, $this->searchEls)) {
      $this->searchEls[] = $name;
      $this->assign('searchElements', $this->searchEls);
    }
  }

  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);

    $parent = $this->controller->getParent();

    $selector = $parent->selector();

    dpm($params);

    if(!empty($params['team_id'])) {
      $parent->set('team_id', $params['team_id']);
    }

    if (!empty($params['member_name'])) {
      $c = CRM_Contact_BAO_Contact::getTableName();
      $e = CRM_Core_BAO_Email::getTableName();
      $selector->join('e', "INNER JOIN (SELECT contact_id, GROUP_CONCAT(DISTINCT email, ', ') emails FROM `$e` GROUP BY contact_id) e ON e.contact_id = c.id"); // Having pulled in the Contact table, we need emails too.
      $selector->where(
        '(c.sort_name LIKE @name OR c.display_name LIKE @name OR e.emails LIKE @name)',
        array('name' => "%{$params['member_name']}%")
      );
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

  public function selector() {
    return $this->controller->getParent()->selector();
  }

  public function mainProcess($allowAJAX = TRUE) {
    parent::mainProcess($allowAJAX);

    $selector = serialize($this->controller->getParent()->selector());
    CRM_Core_Error::debug_var('selector', $selector);
    $this->controller->getParent()->set('selector', $selector);
  }
}
