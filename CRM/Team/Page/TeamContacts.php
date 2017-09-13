<?php

require_once 'CRM/Core/Page.php';

class CRM_Team_Page_TeamContacts extends CRM_Core_Page {
  protected $columns;
  protected $select;

  private $selector;

  public function run() {
    if (!$team_id = CRM_Utils_Request::retrieve('team_id', 'Integer')) {
      CRM_Core_Error::fatal(ts('Required team_id parameter invalid or not provided.'));
    }

    $team = civicrm_api3('Team', 'getsingle', array('id' => $team_id));

    CRM_Utils_System::setTitle(ts('Contacts in Team: %1', array(1 => $team['team_name'])));

    if(!($s_selector && ($this->selector = unserialize($s_selector)) instanceof CRM_Team_Selector_Teams)){
      $this->selector = new CRM_Team_Selector_TeamContacts();
     }

    $this->selector->where('team_id = @team_id', array('team_id' => $team_id));

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
