<?php

require_once 'CRM/Core/Page.php';

class CRM_Team_Page_Teams extends CRM_Core_Page {
  protected $columns;
  protected $select;

  private $limit = 10;
  private $offset = 0;

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Teams'));

    $selector = new CRM_Team_Selector_Teams();

    $controller = new CRM_Core_Selector_Controller(
      $selector,
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

    foreach($selector->getColumnHeaders() as $header) {
      $headers[$header['sort']] = $header['name'];
    }

    $this->assign('colHeaders', $headers);
    $this->assign('rows', $rows);

    parent::run();
  }
}
