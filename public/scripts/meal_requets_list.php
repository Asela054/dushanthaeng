<?php
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

use App\Helpers\EmployeeHelper;

// DB table to use
$table = 'meal_requests';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`issue_type`', 'dt' => 'issue_type', 'field' => 'issue_type'),
    array('db' => '`u`.`meal_name`', 'dt' => 'meal_name', 'field' => 'meal_name'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status'), // Changed dt from 'leave_status' to 'status'
    array(
        'db' => '`u`.`emp_id`', 
        'dt' => 'employee_display', 
        'field' => 'emp_id',
        'formatter' => function($d, $row) {
            $empName = isset($row['emp_name_with_initial']) ? $row['emp_name_with_initial'] : '';
            $callingName = isset($row['calling_name']) ? $row['calling_name'] : '';
            $empId = isset($row['emp_id']) ? $row['emp_id'] : $d;
            
            $employee = (object)[
                'emp_name_with_initial' => $empName,
                'calling_name' => $callingName,
                'emp_id' => $empId
            ];
            return EmployeeHelper::getDisplayName($employee);
        }
    ),
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

  $sql = "SELECT 
        `epa`.*,
        `m`.`meal_name`,
        `e`.`emp_name_with_initial`,
        `e`.`calling_name`
    FROM `meal_requests` AS `epa`
    LEFT JOIN `meal_types` AS `m` ON `epa`.`meal_type` = `m`.`id`
    LEFT JOIN `employees` AS `e` ON `epa`.`emp_id` = `e`.`emp_id`
    WHERE `epa`.`status` IN (1, 2) AND `epa`.`received_status` = 0";

    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>