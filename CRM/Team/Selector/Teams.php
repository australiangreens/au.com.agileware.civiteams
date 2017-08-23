<?php

class CRM_Team_Selector_Teams extends CRM_Core_Selector_Base implements CRM_Core_Selector_API {
  static $_columnHeaders;

  protected $_query;

  protected $_select;

  public function __construct() {
    self::_ensureColHeaders();

    $t = CRM_Team_BAO_Team::getTableName();
    $tc = CRM_Team_BAO_TeamContact::getTableName();

    $this->_query = CRM_Utils_SQL_Select::from("$t t")
      ->join('tc', "LEFT JOIN $tc tc on t.id = tc.team_id")
      ->groupBy('t.id');

    $this->_select = array('t.id', 't.team_name', 't.is_active', 'COUNT(tc.id) AS members');
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

  public function addColumn($sort, $name, $direction = CRM_Utils_Sort::DONTCARE) {
    self::_ensureColHeaders();

    if (empty(self::$_columnHeaders[$sort])){
      self::$_columnHeaders[$sort] = array(
        'name'      => $name,
        'sort'      => $sort,
        'direction' => $direction
      );
    }

    $this->_select[] = $sort;
  }

  public function getPagerParams($action, &$params) {
    $params['csvString'] = NULL;
    $params['rowCount'] = CRM_Utils_Pager::ROWCOUNT;
    $params['status'] = ts('Teams %%StatusMessage%%');
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
  }

  /*public function &getSortOrder($action) {
    }*/

  public function &getColumnHeaders($action = NULL, $type = NULL) {
    self::_ensureColHeaders();
    return self::$_columnHeaders;
  }

  public function getTotalCount($action) {
    $query = $this->_query->copy();

    $query->select('COUNT(DISTINCT t.id)');

    return CRM_Core_DAO::singleValueQuery($query->toSQL());
  }

  public function &getRows($action, $offset, $rowCount, $sort, $type = NULL){
    $rows = array();

    $result = new CRM_Team_BAO_Team();

    $query = $this->_query->copy();
    $query -> limit($rowCount, $offset)
           -> select($this->_select);

    $result->query($query->toSql());

    while($result->fetch()){
      $row['id']        = $result->id;
      $row['team_name'] = $result->team_name;
      $row['members']   = $result->members;
      $row['status']    = $result->is_active;

      $rows[] = $row;
    }

    return $rows;
  }

  /*public function getTemplateFileName($action = NULL){
    }*/

  public function getExportFileName($type = 'csv'){
    return ts('CiviTeams Teams');
  }
}