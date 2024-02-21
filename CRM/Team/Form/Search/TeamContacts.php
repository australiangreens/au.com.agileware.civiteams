<?php

/**
 * A custom contact search
 */
class CRM_Team_Form_Search_TeamContacts extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
  function __construct(&$formValues) {
    parent::__construct($formValues);
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(ts('Team Contacts'));

    $form->addEntityRef('team_id', ts('Team'), array (
        'entity' => 'team',
        'placeholder' => ts('- Select Team -'),
        'select' => array('minimumInputLength' => 0),
        'api' => array (
          'search_field' => 'team_name',
          'label_field' => 'team_name'
        )
      )
    );

    $form->add('text', 'name', ts('Name or Email'));

    // Optionally define default search values
    $form->setDefaults(array(
      'team_id' => NULL,
    ));

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $form->assign('elements', array('team_id', 'name'));

    $params = $form->controller->exportValues($this->_name);

    $team_id = $params['team_id'];

    if (!empty($team_id) || $team_id = CRM_Utils_Request::retrieve('team_id', 'Integer')) {
      $defaults = array('team_id' => $team_id);
      $form->setDefaults($defaults);

      $team = civicrm_api3('Team', 'getsingle', array('id' => $team_id));
      if(!CRM_Team_BAO_Team::hasTeamAccess($team)) {
        throw new \Civi\API\Exception\UnauthorizedException('Permission denied to access contacts of this team.');
      }

      CRM_Utils_System::setTitle(ts('Contacts in Team: %1', array(1 => $team["team_name"])));

      $form->assign('team_id', $team_id);
    }
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
    return NULL;
    // return array(
    //   'summary' => 'This is a summary',
    //   'total' => 50.0,
    // );
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  function &columns() {
    // return by reference
    $columns = array(
      ts('Name') => 'sort_name',
      ts('Address') => 'street_address',
      ts('City') => 'city',
      ts('State') => 'state_province',
      ts('Postal') => 'postal_code',
      ts('Country') => 'country',
      ts('Email') => 'email',
      ts('Phone') => 'phone',
    );
    return $columns;
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   * @return string, sql
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.
    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    return "
      DISTINCT
      contact_a.id           AS contact_id,
      contact_a.contact_type AS contact_type,
      contact_a.sort_name    AS sort_name,
      address.street_address,
      address.city,
      address.postal_code,
      state_province.name    AS state_province,
      country.name           AS country,
      email.email,
      phone.phone
    ";
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "
      FROM       civicrm_contact contact_a
      INNER JOIN civicrm_team_contact team_contact     ON ( team_contact.contact_id  = contact_a.id )
      LEFT JOIN  civicrm_address address               ON ( address.contact_id       = contact_a.id AND
                                                            address.is_primary       = 1 )
      LEFT JOIN  civicrm_email `email`                 ON ( `email`.contact_id       = contact_a.id AND
                                                            `email`.is_primary       = 1 )
      LEFT JOIN  civicrm_phone phone                   ON ( phone.contact_id         = contact_a.id AND
                                                            phone.is_primary         = 1 )
      LEFT JOIN  civicrm_state_province state_province ON ( state_province.id        = address.state_province_id )
      LEFT JOIN  civicrm_country country               ON ( country.id               = address.country_id )
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = array();

    $count  = 1;
    $clause = array();
    $team_id = CRM_Utils_request::retrieve('team_id', 'Integer');
    $clause[] = "team_contact.status = 1";

    if ($team_id != NULL) {
      $params[$count] = array($team_id, 'Integer');
      $clause[] = "team_contact.team_id = %{$count}";
      $count++;
    }

    $name = CRM_Utils_request::retrieve('name', 'String');
    if ($name != NULL) {
      $params[$count] = array('%' . $name . '%', 'String');
      $clause[] = "contact_a.sort_name LIKE %{$count} OR contact_a.display_name LIKE %{$count} OR email.email LIKE %{$count}";
      $count++;
    }

    if (!empty($clause)) {
      $where = implode(' AND ', $clause);
    } else {
      $where = '1';
    }

    return $this->whereClause($where, $params);
  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Team/Form/Search/TeamContacts.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */
  function alterRow(&$row) {
  }
}
