<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Team_Form_Teams extends CRM_Core_Form {
  protected $columns;
  protected $select;

  private $limit = 10;
  private $offset = 0;

  public function buildQuickForm() {
    // Team Name Form Entry.
    $this->add('text', 'team_name', ts('Team Name'));

    $this->add('text', 'sort_name', ts('Member Name or Email'));

    $this->addCheckBox('status', ts('Status'),
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

    $this->add('select', 'limit', ts('Show entries'), array(10 => 10, 25 => 25, 50 => 50, 100 => 100));

    $this->addOutputColumn('team_name', ts('Team Name'));
    $this->addOutputColumn('members', ts('Members'));

    // export form elements
    $this->assign('searchElements', ['team_name','sort_name','status']);

    $this->assign('colHeaders', array_values($this->columns));
    $this->assign('colKeys', array_keys($this->columns));

    CRM_Core_Session::setStatus(json_encode($this->controller->exportValues($this->_name)));

    parent::buildQuickForm();
  }

  public function postProcess() {
    $input = $this->controller->exportValues($this->_name);

    CRM_Core_Session::setStatus(kpr($input, TRUE));

    if(!empty($input['team_name'])) {
      $this->teamQuery()->where('t.team_name like @name', array('name' => "%{$input['team_name']}%"));
    }

    if(!empty($input['status'])) {
      $status = array();
      if(!empty($input['status']['enabled'])) {
        $status[] = 1;
      }
      if(!empty($input['status']['disabled'])) {
        $status[] = 0;
      }
      $this->teamQuery()->where('t.is_active IN (@status)', array('status' => $status));
    }
  }


  public function addOutputColumn(string $name, string $label) {
    $this->columns[$name] = $label;
  }

  public function teamQuery() {
    return $this->select;
  }

  /* We build the basic query in the constructor so that preProcess hooks can alter it. */
  public function __construct() {
    parent::__construct();

    $t = CRM_Team_BAO_Team::getTableName();
    $tc = CRM_Team_BAO_TeamContact::getTableName();

    $this->select = CRM_Utils_SQL_Select::from("$t t")
      ->join('tc', "LEFT JOIN $tc tc on t.id = tc.team_id")
      ->select(array('t.id', 't.team_name', 't.is_active', 'COUNT(tc.id) AS mcount'))
      ->groupBy('t.id')
      ->orderBy('t.id ASC')
      ->limit($this->limit, $this->offset);
  }

  public function teamList() {
    $team_list = array();

    $sql = $this->select->toSQL();

    CRM_Core_Session::setStatus($sql, __FUNCTION__ . '「$sql」');

    $team = new CRM_Team_BAO_Team();

    $team->query($this->select->toSQL());

    while($team->fetch()) {
      $team_list[] = $this_team = array(
        'id' => $team->id,
        'team_name' => $team->team_name,
        'members' => $team->mcount,
      );
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
