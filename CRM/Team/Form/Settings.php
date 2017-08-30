<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm() {
    $team_id = CRM_Utils_Request::retrieve('team_id', 'Integer');

    $team = civicrm_api3('Team', 'getsingle', array('id' => $team_id));

    $this->assign('team_name', $team['team_name']);
    $this->assign('is_domain', !! $team['domain_id']);

    $defaults = array (
      'team_name' => $team['team_name'],
      'enabled' => $team['is_active'],
    );

    // add form elements
    $this->add(
      'text', // field type
      'team_name', // field name
      ts('Name'), // field label
      '',
      TRUE // is required
    );

    $this->add(
      'checkbox',
      'enabled',
      ts('Enabled')
    );

    $this->add(
      'hidden',
      'team_id'
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
        'isDefault' => FALSE,
      ),
    ));

    $this->setDefaults($defaults);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    CRM_Core_Session::setStatus(kpr($values, TRUE), __FUNCTION__ . '::「$values」');

    parent::postProcess();
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
