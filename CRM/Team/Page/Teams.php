<?php

require_once 'CRM/Core/Page.php';

class CRM_Team_Page_Teams extends CRM_Core_Page {
  protected $columns;
  protected $select;

  private $selector;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Teams'));

    $form = new CRM_Core_Controller_Simple(
      'CRM_Team_Form_Teams',
      ts('Find Teams'),
      CRM_Core_Action::ADD
    );
    list(, $action) = $form->getActionName();

    if($action != 'refresh') {
      $s_selector = $this->get('selector');
    }

    if(!($s_selector && ($this->selector = unserialize($s_selector)) instanceof CRM_Team_Selector_Teams)){
      $this->selector = new CRM_Team_Selector_Teams();
    }

    $form->setEmbedded(TRUE);
    $form->setParent($this);
    $form->process();
    $form->run();

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


    $session = CRM_Core_Session::singleton();

    $urlString = 'civicrm/teams';
    $urlParams = 'reset=1';
    $url = CRM_Utils_System::url($urlString, $urlParams);
    $session->pushUserContext($url);

    return parent::run();
  }

  public function &selector() {
    return $this->selector;
  }
}
