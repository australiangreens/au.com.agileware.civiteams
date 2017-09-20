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

    if(($team_id = CRM_Utils_Request::retrieve('team_id' , 'Integer')) || ($team_id = $this->controller->getParent()->get('team_id'))) {
      $defaults['team_id'] = $team_id;
    }

    if($member_name = $this->controller->getParent()->get('member_name')) {
      $defaults['member_name'] = $member_name;
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

    if(!empty($params['team_id'])) {
      $parent->set('team_id', $params['team_id']);
    }

    if (!empty($params['member_name'])) {
      $parent->set('member_name', $params['member_name']);
    } else {
      $parent->set('member_name', '');
    }
  }

  public function selector() {
    return $this->controller->getParent()->selector();
  }

  public function mainProcess($allowAJAX = TRUE) {
    parent::mainProcess($allowAJAX);

    $selector = serialize($this->controller->getParent()->selector());

    $this->controller->getParent()->set('selector', $selector);
  }
}
