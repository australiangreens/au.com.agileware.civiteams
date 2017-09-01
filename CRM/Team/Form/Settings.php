<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_Settings extends CRM_Core_Form {
  private $groupNames;
  private $elementNames;
  private $descriptions;

  public function __construct() {
    $this->groupNames = array();
    $this->elementNames = array();
    $this->descriptions = array();

    $this->team_id = CRM_Utils_Request::retrieve('team_id', 'Integer');

    return parent::__construct();
  }

  public function buildQuickForm() {
    $team = civicrm_api3('Team', 'getsingle', array('id' => $this->team_id));

    CRM_Utils_System::setTitle(ts('Team Settings: %1', array(1 => $team['team_name'])));

    $this->assign('team_name',    $team['team_name']);
    $this->assign('is_domain', !! $team['domain_id']);
    $this->assign('groupNames',   $this->groupNames);
    $this->assign('elementNames', $this->elementNames);
    $this->assign('descriptions', $this->descriptions);
    $this->assign('baseURL',      CRM_Core_Config::singleton()->userFrameworkBaseURL);

    $defaults = array (
      'team_name' => $team['team_name'],
      'enabled'   => $team['is_active'],
      'team_id'   => $this->team_id,
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
        'type'      => 'submit',
        'name'      => ts('Save'),
        'isDefault' => TRUE,
      ),
      array(
        'type'      => 'cancel',
        'name'      => ts('Cancel'),
        'isDefault' => FALSE,
      ),
    ));

    $this->setDefaults($defaults);

    parent::buildQuickForm();
  }

  public function add_elementGroup($key, $name) {
    $this->groupNames[$key] = $name;
  }

  public function add_elementName($key, $group, $description = NULL) {
    $this->elementNames[$group][] = $key;
    $this->descriptions[$key] = $description;
  }

  public function postProcess() {
    $values = $this->exportValues();

    $params = array (
      'id'        => $values['team_id'],
      'team_name' => $values['team_name'],
      'is_active' => !! $values['enabled'],
    );

    $result = civicrm_api3('Team', 'create', $params);

    $this->team_id = $result['id'];

    parent::postProcess();

    $url = CRM_Utils_System::url('civicrm/teams/settings', 'reset=1&team_id=' . $values['team_id']);
    CRM_Utils_System::redirect($url);
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
