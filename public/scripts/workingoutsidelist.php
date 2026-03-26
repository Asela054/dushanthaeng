<?php
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

// DB table to use
$table = 'employee_outside_working';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => 'u.id', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => 'ua.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
    array( 'db' => 'u.date', 'dt' => 'date', 'field' => 'date' ),
    array( 'db' => 'u.location', 'dt' => 'location', 'field' => 'location' ),
    array( 'db' => 'u.remark', 'dt' => 'remark', 'field' => 'remark' ),
    array( 'db' => 'u.amount', 'dt' => 'amount', 'field' => 'amount' ),
    array( 'db' => 'u.status', 'dt' => 'status', 'field' => 'status' ),
    array( 'db' => 'ua.calling_name', 'dt' => 'calling_name', 'field' => 'calling_name' ),
    array( 'db' => 'u.emp_id', 'dt' => 'employee_display', 'field' => 'emp_id', 
          'formatter' => function($d, $row) {
              $employee = (object)[
                  'emp_name_with_initial' => $row['emp_name_with_initial'],
                  'calling_name' => $row['calling_name'],
                  'emp_id' => $row['emp_id']
              ];
              
              return EmployeeHelper::getDisplayName($employee);
          }
    )
);

// SQL server connection information
require('config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$joinQuery = "FROM `employee_outside_working` AS `u` 
LEFT JOIN `employees` AS `ua` ON `ua`.`emp_id` = `u`.`emp_id`";
    
$extraWhere = "`u`.`status` IN (1, 2) AND 1=1";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);