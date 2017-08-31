<?php

require_once 'CRM/Core/Page.php';

class CRM_Team_Page_Teams extends CRM_Core_Page {
  protected $columns;
  protected $select;

  private $selector;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Teams'));

    $this->selector = new CRM_Team_Selector_Teams();

    $form = new CRM_Core_Controller_Simple(
      'CRM_Team_Form_Teams',
      ts(' Find Teams'),
      CRM_Core_Action::ADD
    );
    $form->setEmbedded(TRUE);
    $form->setParent($this);
    $form->process();
    $form->run();

    $urlString = 'civicrm/teams';
    $urlParams = 'reset=1';

    if ($team_name = CRM_Utils_Request::retrieve('team_name', 'String')) {
      $urlParams .= '&team_name=' . $team_name;
    }
    if($member_name = CRM_Utils_Request::retrieve('member_name', 'String')) {
      $urlParams .= '&team_name=' . $team_name;
    }
    if(is_array($status = CRM_Utils_Request::retrieve('status', 'String'))) {
      foreach($status as $k => $v) {
        $urlParams .= '&status[' . $k . ']=' . $v;
      }
    }

    $session = CRM_Core_Session::singleton();

    $url = CRM_Utils_System::url($urlString, $urlParams);

    $session->replaceUserContext($url);

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

  public function selector() {
    return $this->selector;
  }
}