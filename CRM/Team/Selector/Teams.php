<?php

use Civi\Api4\Managed;

class CRM_Team_Selector_Teams extends CRM_Core_Selector_Base implements CRM_Core_Selector_API {
  static $_columnHeaders;
  static $_links;

  protected $_query;

  protected $_select;
  protected $_groupBy;

  public function __construct() {
    self::_ensureColHeaders();

    $t = CRM_Team_BAO_Team::getTableName();
    $tc = CRM_Team_BAO_TeamContact::getTableName();

    $this->_query = CRM_Utils_SQL_Select::from("`$t` t")
      ->join('tc', "LEFT JOIN `$tc` tc on t.id = tc.team_id");

    $this->_select = array('t.id', 't.team_name', 't.is_active', 'COUNT(case tc.status when 1 then 1 else null end) AS members');
    $this->_groupBy = array('t.id');
    $this->where(array(
      "t.domain_id = #id OR t.domain_id IS NULL",
    ), array(
      "id" => CRM_Core_Config::domainID()
    ));
  }

  private static function _ensureColHeaders () {
    if(empty(self::$_columnHeaders)) {
      self::$_columnHeaders = array(
        'team_name' => array(
          'name'      => ts('Team Name'),
          'sort'      => 'team_name',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'members' => array(
          'name'      => ts('Members'),
          'sort'      => 'members',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
      );
    }
  }

  public function where($exprs, $args = NULL) {
    return $this->_query->where($exprs, $args);
  }

  public function join($name, $exprs, $args = NULL) {
    return $this->_query->join($name, $exprs, $args);
  }

  public function having($exprs, $args = NULL) {
    return $this->_query->having($exprs, $args);
  }

  public function addColumn($sort, $name, $expr = NULL, $direction = CRM_Utils_Sort::DONTCARE) {
    self::_ensureColHeaders();

    if(!$expr) {
      $expr = $sort;
    }

    if (empty(self::$_columnHeaders[$sort])){
      self::$_columnHeaders[$sort] = array(
        'name'      => $name,
        'sort'      => $sort,
        'direction' => $direction
      );
    }

    $this->_select[] = $expr;
  }

  public function getPagerParams($action, &$params) {
    $params['csvString'] = NULL;
    $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
    $params['status'] = ts('Team %%StatusMessage%%');
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
  }

  public function &getColumnHeaders($action = NULL, $type = NULL) {
    self::_ensureColHeaders();
    return self::$_columnHeaders;
  }

  public function getTotalCount($action) {
    $query = $this->_query->copy();

    $query->select('COUNT(DISTINCT t.`id`)');

    $total = CRM_Core_DAO::singleValueQuery($query->toSQL());

    return $total;
  }

  public function &getRows($action, $offset, $rowCount, $sort, $type = NULL){
    $rows = array();

    $result = new CRM_Team_BAO_Team();

    $query = $this->_query->copy();
    $query->limit($rowCount, $offset)
          ->select($this->_select)
          ->groupBy($this->_groupBy);

    $order = $sort->orderBy();

    $qfKey = $this->_key;

    if(!empty($order)) {
      $query->orderBy($sort->orderBy());
    }
    else {
      $query->orderBy('t.`created` ASC');
    }

    $result->query($query->toSql());

    $permissions = array();
    if (!CRM_Core_Permission::check('administer civiteams')) {
      $permissions[] = CRM_Core_Action::UPDATE;
      $permissions[] = CRM_Core_Action::DELETE;
    }
    $mask = CRM_Core_Action::mask($permissions);

    while($result->fetch()){
      $row['id']        = $result->id;
      $row['status']    = $result->is_active;

      foreach(self::$_columnHeaders as $key => $col) {
        $row[$key] = htmlentities($result->{$key});
      }

      $row['actions'] = CRM_Core_Action::formLink(
        self::links($qfKey),
        $mask,
        array('team_id' => $result->id),
        ts('more'),
        FALSE,
        'team.row',
        'Team',
        $result->id
      );

      $rows[] = $row;
    }

    return $rows;
  }

  /*public function getTemplateFileName($action = NULL){
    }*/

  public function getExportFileName($type = 'csv'){
    return ts('CiviTeams Teams');
  }

  public static function &links() {
    if(!(self::$_links)) {
      [$key] = func_get_args();
      $extraParams = ($key) ? "&key={$key}" : NULL;

      $csid = Managed::get(FALSE)
                   ->addWhere('module', '=', 'au.com.agileware.civiteams')
                   ->addWhere('name', '=', 'CRM_Team_Form_Search_TeamContacts')
                   ->addSelect('entity_id')
                   ->execute()
                   ->first()['entity_id'];

      self::$_links = array(
        CRM_Core_Action::VIEW => array(
          'name' => ts('Contacts'),
          'url' => 'civicrm/contact/search/custom',
          'qs' => "reset=1&force=1&team_id=%%team_id%%&csid={$csid}{$extraParams}",
          'title' => ts('List Member Contacts'),
          ),
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Settings'),
          'url' => 'civicrm/teams/settings',
          'qs' => "reset=1&action=update&team_id=%%team_id%%{$extraParams}",
          'title' => ts('Edit Team Settings'),
        ),
        /*CRM_Core_Action::DELETE => array(
          'name' => ts('Disable'),
          'url' => 'civicrm/teams/disable',
          'qs' => "reset=1&team_id=%%team_id%%{$extraParams}",
          'title' => ts('Disable Team'),
          ),*/
      );
    }

    return self::$_links;
  }
}
