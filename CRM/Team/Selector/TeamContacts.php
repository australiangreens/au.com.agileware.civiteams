<?php

class CRM_Team_Selector_TeamContacts extends CRM_Core_Selector_Base implements CRM_Core_Selector_API {
  static $_columnHeaders;
  static $_links;

  protected $_query;

  protected $_select;
  protected $_groupBy;

  public function __construct() {
    self::_ensureColHeaders();

    $tc = CRM_Team_BAO_TeamContact::getTableName();
    $t  =        CRM_Team_BAO_Team::getTableName();
    $c  =  CRM_Contact_BAO_Contact::getTableName();
    $e  =       CRM_Core_BAO_Email::getTableName();
    $p  =       CRM_Core_BAO_Phone::getTableName();
    $a  =     CRM_Core_BAO_Address::getTableName();

    $this->_query = CRM_Utils_SQL_Select::from("`$c` c")
      ->join('tc', "INNER JOIN `$tc` tc on tc.contact_id = c.id")
      ->join('e',  "LEFT  JOIN `$e`   e on  e.contact_id = c.id and e.is_primary <> 0")
      ->join('p',  "LEFT  JOIN `$p`   p on  p.contact_id = c.id and p.is_primary <> 0")
      ->join('a',  "LEFT  JOIN `$a`   a on  a.contact_id = c.id and a.is_primary <> 0");

    $this->_select = array(
      'tc.id', 'tc.team_id', 'tc.contact_id', 'c.display_name', 'c.sort_name',
      'a.street_address', 'a.supplemental_address_1', 'a.supplemental_address_2', 'a.supplemental_address_3',
      'a.city', 'a.state_province_id', 'a.postal_code', 'a.country_id',
      'e.email', 'p.phone', 'p.phone_ext', 'p.phone_numeric',
    );
  }

  private static function _ensureColHeaders () {
    if(empty(self::$_columnHeaders)) {
      self::$_columnHeaders = array(
        'sort_name' => array(
          'name'      => ts('Name'),
          'sort'      => 'sort_name',
          'direction' => CRM_Utils_Sort::ASCENDING,
        ),
        'street_address' => array(
          'name'      => ts('Address'),
          'sort'      => 'street_address',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'city' => array(
          'name'      => ts('City'),
          'sort'      => 'city',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'state_province_id' => array(
          'name'      => ts('State'),
          'sort'      => 'state_province_id',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'postal_code' => array(
          'name'      => ts('Postal'),
          'sort'      => 'postal_code',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'country_id' => array(
          'name'      => ts('Country'),
          'sort'      => 'country_id',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'email' => array(
          'name'      => ts('Email'),
          'sort'      => 'email',
          'direction' => CRM_Utils_Sort::DONTCARE,
        ),
        'phone_numeric' => array(
          'name'      => ts('Phone'),
          'sort'      => 'phone_numeric',
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
    $params['status'] = ts('Contact %%StatusMessage%%');
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
  }

  public function &getColumnHeaders($action = NULL, $type = NULL) {
    self::_ensureColHeaders();
    return self::$_columnHeaders;
  }

  public function getTotalCount($action) {
    $query = $this->_query->copy();

    $query->select('COUNT(DISTINCT tc.`id`)');

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
      $query->orderBy('c.`sort_name` ASC');
    }

    $result->query($query->toSql());

    $permissions = array();
    $mask = CRM_Core_Action::mask($permissions);

    while($result->fetch()){
      $row = array('id' => $result->id);

      $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->id;

      foreach(self::$_columnHeaders as $key => $col) {
        switch($key) {
          case 'sort_name':
            $row[$key] = htmlentities($result->display_name);
            break;
          case 'street_address':
            $row[$key] = htmlentities($result->street_address);
            foreach([1, 2, 3] as $i) {
              if (!empty($result->{'supplemental_address_' . $i})){
                $row[$key] .= '<br />' . htmlentities($result->{'supplemental_address_' . $i});
              }
            }
            break;
          case 'state_province_id':
            if (!empty($result->state_province_id)) {
              $row[$key] = CRM_Core_PseudoConstant::stateProvinceAbbreviation($result->state_province_id);
            }
            break;
          case 'country_id':
            if (!empty($result->country_id)) {
              $row[$key] = CRM_Core_PseudoConstant::country($result->country_id);
            }
            break;
          case 'phone_numeric':
            if(!empty($result->phone)) {
              $row[$key] = htmlentities($result->phone);
            }
            if(!empty($result->phone_ext)) {
              $row[$key] .= ' ' . ts('ext.') . ' ' . htmlentities($result->phone_ext);
            }
            break;
          default:
            $row[$key] = htmlentities($result->{$key});
            break;
        }

      }

      $row['actions'] = CRM_Core_Action::formLink(
        self::links($qfKey),
        $mask,
        array(
          'cid' => $result->contact_id,
          'team_id' => $result->team_id,
          'id' => $result->id,
        ),
        ts('more'),
        FALSE,
        'contact.row',
        'Contact',
        $result->id
      );

      $rows[] = $row;
    }

    return $rows;
  }

   public function getExportFileName($type = 'csv'){
    return ts('CiviTeams Contacts');
  }

  public static function &links() {
    if(!(self::$_links)) {
      list($key) = func_get_args();
      $extraParams = ($key) ? "&key={$key}" : NULL;

      self::$_links = array(
        CRM_Core_Action::VIEW => array(
          'name' => ts('View'),
          'url' => 'civicrm/contact/view',
          'qs' => "reset=1&force=1&team_id=%%team_id%%&cid=%%cid%%{$extraParams}",
          'title' => ts('View Contact Details'),
          ),
        CRM_Core_Action::UPDATE => array(
          'name' => ts('Edit'),
          'url' => 'civicrm/contact/add',
          'qs' => "reset=1&action=update&team_id=%%team_id%%&cid=%%cid%%{$extraParams}",
          'title' => ts('Edit Contact Details'),
        ),
        CRM_Core_Action::DELETE => array(
          'name' => ts('Remove'),
          'url' => '#',
          'qs' => "reset=1&id=%%id%%{$extraParams}",
          'title' => ts('Remove Contact From Team'),
        ),
      );
    }

    return self::$_links;
  }
}