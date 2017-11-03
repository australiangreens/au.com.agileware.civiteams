<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_AddContacts extends CRM_Contact_Form_Task {
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
    $params["team_id"] = CRM_Utils_Request::retrieve('team_id', 'Integer');

    $added = 0;
    $existing = 0;

    $teamName = civicrm_api3('Team', 'getvalue', array('id' => $params['team_id'], 'return' => 'team_name'));

    foreach($this->_contactIds as $cid) {
      if (civicrm_api3('TeamContact', 'getcount', array('team_id' => $params['team_id'], 'contact_id' => $cid))) {
        $existing++;
      }
      else {
        civicrm_api3('TeamContact', 'create', array('team_id' => $params['team_id'], 'contact_id' => $cid, 'status' => 1));
        $added++;
      }
    }

    $status = array(
      ts('%count contact added to team', array(
          'count' => $added,
          'plural' => '%count contacts added to team',
      )),
    );
    if ($existing) {
      $status[] = ts('%count contact was already in team', array(
          'count' => $existing,
          'plural' => '%count contacts were already in team',
        ));
    }
    $status = '<ul><li>' . implode('</li><li>', $status) . '</li></ul>';
    CRM_Core_Session::setStatus($status, ts('Added Contact to %1', array(
          1 => $teamName,
          'count' => $added,
          'plural' => 'Added Contacts to %1',
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
