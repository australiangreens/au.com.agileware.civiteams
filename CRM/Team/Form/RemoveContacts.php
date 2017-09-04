<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_RemoveContacts extends CRM_Contact_Form_Task {
  public function buildQuickForm() {
    $this->addEntityRef(
      'team_id',
      ts('Team'),
      array(
        'entity' => 'team',
        'select' => array('minimumInputLength' => 0),
        'api' => array(
          'search_field' => 'team_name',
          'label_field' => 'team_name',
        ),
      ),
      TRUE
    );

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $params = $this->controller->exportValues();

    $deleted = 0;
    $total = count($this->_contactIds);

    $teamName = civicrm_api3('Team', 'getvalue', array('id' => $params['team_id'], 'return' => 'team_name'));

    $deletable = civicrm_api3(
      'TeamContact', 'get',
      array(
        'team_id' => $params['team_id'],
        'contact_id' => array('IN' => $this->_contactIds),
        'return' => 'id',
        'options' => array('limit' => 0),
      )
    );

    foreach(array_keys($deletable['values']) as $delid) {
      $deleted++;
      civicrm_api3('TeamContact', 'delete', array('id' => $delid));
    }

    $status = array(
      ts('%count contact removed from team', array(
          'count' => $deleted,
          'plural' => '%count contacts removed from team',
      )),
    );
    if ($deleted < $total) {
      $status[] = ts('%count contact not found in team', array(
          'count' => ($total - $deleted),
          'plural' => '%count contacts not found in team',
        ));
    }
    $status = '<ul><li>' . implode('</li><li>', $status) . '</li></ul>';
    CRM_Core_Session::setStatus($status, ts('Removed Contact from %1', array(
          1 => $teamName,
          'count' => $deleted,
          'plural' => 'Removed Contacts from %1',
          )), 'success', array('expires' => 0));
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
