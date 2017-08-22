<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_Teams extends CRM_Core_Form {
  public function buildQuickForm() {
    // Team Name Form Entry.
    $this->add(
      'text',
      'team_name',
      ts('Team Name')
    );

    $this->add(
      'text',
      'sort_name',
      ts('Member Name or Email')
    );

    $this->addCheckBox(
      'status',
      ts('Status'),
      array(ts('Enabled') => 'enabled', ts('Disabled') => 'disabled'),
      NULL, NULL, NULL, NULL, "\n", FALSE
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Search'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('searchElements', ['team_name','sort_name','status']);

    $this->assign('teamList', $this->teamList());

    parent::buildQuickForm();
  }

  public function postProcess() {
  }

  public function teamList() {
    $input = $this->exportValues();

    $team_list = array();

    $team = new CRM_Team_BAO_Team();
    $contact = new CRM_Team_BAO_TeamContact();

    $t = $team->tableName();
    $tc = $contact->tableName();

    $sql = "SELECT t.id, t.team_name, COUNT(tc.id) AS mcount FROM {$t} t LEFT JOIN {$tc} tc ON tc.team_id = t.id";

    $sql .= ' GROUP BY t.id';

    $team->query($sql);

    $team->find();

    while($team->fetch()) {
      $team_list[] = $this_team = array(
        'id' => $team->id,
        'team_name' => $team->team_name,
        'members' => $team->mcount,
      );

      CRM_Core_Session::setstatus(json_encode($this_team, JSON_PRETTY_PRINT));
    }

    return $team_list;
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
