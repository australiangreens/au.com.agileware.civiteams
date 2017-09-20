<?php

require_once 'CRM/Core/Page.php';

class CRM_Team_Page_TeamContacts extends CRM_Core_Page {
  protected $columns;
  protected $select;

  private $selector;

  public function run() {
    $team_id = CRM_Utils_Request::retrieve('team_id', 'Integer');

    if (!$team_id && !($team_id = $this->get('team_id'))) {
      CRM_Core_Error::fatal(ts('Required team_id parameter invalid or not provided.') . '<pre>' . print_r($this->selector, TRUE) . '</pre>');
    }

    $team = civicrm_api3('Team', 'getsingle', array('id' => $team_id));

    CRM_Utils_System::setTitle(ts('Contacts in Team: %1', array(1 => $team['team_name'])));

    $form = new CRM_Core_Controller_Simple(
      'CRM_Team_Form_TeamContacts',
      ts('Find Contacts within this team'),
      CRM_Core_Action::ADD
    );
    list(, $action) = $form->getActionName();

    if($action != 'refresh') {
      $s_selector = $this->get('selector');
    }

    if(!($s_selector && ($this->selector = unserialize($s_selector)) instanceof CRM_Team_Selector_Teams)){
      $this->selector = new CRM_Team_Selector_TeamContacts();
     }

    $this->selector->where('team_id = @team_id', array('team_id' => $team_id));

    if($member_name = $this->get('member_name')) {
      $this->selector->where(
        '(c.sort_name LIKE @name OR c.display_name LIKE @name OR e.email LIKE @name)',
        array('name' => "%{$member_name}%")
      );
    }

    $form->setEmbedded(TRUE);
    $form->setParent($this);
    $form->process();
    $form->run();

    $session = CRM_Core_Session::singleton();

    $urlString = 'civicrm/teams/contacts';
    $url = CRM_Utils_System::url($urlString, $urlParams);
    $session->pushUserContext($url);

    $controller = new CRM_Core_Selector_Controller(
      $this->selector,
      $this->get(CRM_Utils_Pager::PAGE_ID),
      $this->get(CRM_Utils_Sort::SORT_ID) . $this->get(CRM_Utils_Sort::SORT_DIRECTION),
      CRM_Core_Action::VIEW,
      $this,
      CRM_Core_Selector_Controller::TEMPLATE
    );

    $controller->setEmbedded(TRUE);
    $controller->run();

    $rows = $controller->getRows($controller);

    $headers = array();

    foreach($this->selector->getColumnHeaders() as $header) {
      $headers[$header['sort']] = $header;
    }

    $this->assign('colHeaders', $headers);
    $this->assign('rows', $rows);

    return parent::run();
  }

  public function &selector() {
    return $this->selector;
  }

}
