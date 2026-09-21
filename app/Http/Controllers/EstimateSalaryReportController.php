<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;
use DateTime;
use PDF;
use App\EmployeePayslip;

class EstimateSalaryReportController extends Controller
{
    public function index(Request $request)
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }
        $companies = DB::table('companies')->select('*')->get();

        return view('Report.estimate_salary_report', compact('companies'));
    }

    public function generatereport(Request $request)
    {
        $permission = Auth::user()->can('department-wise-leave-report');
        if (!$permission) {
            abort(403);
        }

        $reportType = $request->input('reporttype');
        $companyId = $request->input('company');
        $departmentId = $request->input('department');
        $month = $request->input('selectedmonth');
        $fromDate = Carbon::parse($month)->startOfMonth()->toDateString(); 
        $toDate   = Carbon::parse($month)->endOfMonth()->toDateString();  

        // Validate the input based on the report type
        if ($reportType == '1' && empty($month)) {
            return response()->json(['error' => 'Please select a month.'], 400);
        } elseif ($reportType == '2' && (empty($fromDate) || empty($toDate))) {
            return response()->json(['error' => 'Please select both from and to dates.'], 400);
        }

        // Step 1: Fetch Employee Data (unchanged)
        $employees = DB::select("
            SELECT emp.id, emp.emp_id, emp.emp_etfno, emp.emp_fullname, emp.emp_gender, 
                dept.name AS departmentname,cam.name AS companyname, job.title AS jobtitlename, emp.emp_shift, 
                COALESCE(esd_shift.shift_name, st.shift_name) AS shiftname
            FROM employees emp
            LEFT JOIN departments dept ON emp.emp_department = dept.id
            LEFT JOIN companies cam ON emp.emp_company = cam.id
            LEFT JOIN job_titles job ON emp.emp_job_code = job.id
            LEFT JOIN shift_types st ON emp.emp_shift = st.id
            LEFT JOIN employeeshiftdetails esd 
                ON esd.emp_id = emp.id
            LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
            WHERE emp.deleted = 0
            AND emp.is_resigned = 0
            AND emp.emp_department = ? 
            ORDER BY emp.id ASC",
            [$departmentId]
        );

        // Step 2: Generate date range in PHP (alternative to recursive CTE)
        $startDate = new DateTime($fromDate);
        $endDate = new DateTime($toDate);
        $dateRange = [];
        
        while ($startDate <= $endDate) {
            $dateRange[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }
        
        $employeeData = [];
        foreach ($employees as $employee) {
            $work_days = 0;
            $leave_days = 0;
            $no_pay_days = 0;
            $normal_ot_hours = 0; 
            $double_ot_hours = 0;

            if(!empty($employee->emp_shift)){
                // Initialize attendance records array
                $attendanceRecords = [];
                
                // Process each date in the range
                foreach ($dateRange as $date) {
                    $record = DB::select("
                        SELECT 
                            DATE_FORMAT(?, '%Y-%m-%d') AS in_date,
                            DATE_FORMAT(?, '%Y-%m-%d') AS out_date,
                            COALESCE(h.holiday_name, 
                                CASE WHEN WEEKDAY(?) IN (5,6) THEN DAYNAME(?) 
                                ELSE 'Weekday' END) AS day_type,
                            COALESCE(esd_shift.shift_name, st.shift_name) AS shift,
                            DATE_FORMAT(MIN(att.timestamp), '%h:%i %p') AS in_time, 
                            DATE_FORMAT(MAX(att.timestamp), '%h:%i %p') AS out_time,
                            ROUND(COALESCE(la.minites_count, 0), 2) AS late_min, 
                            COALESCE(leave_data.leavename, '') AS leave_type, 
                            ROUND(COALESCE(leave_data.no_of_days, 0), 2) AS leave_days,
                            ROUND(COALESCE(ot.hours, 0) + COALESCE(ot.holiday_normal_hours, 0), 2) AS ot_hours,
                            ROUND(COALESCE(ot.double_hours, 0), 2) AS double_ot,
                            ROUND(COALESCE(ot.triple_hours, 0), 2) AS triple_ot
                        FROM (SELECT ? AS date) dr
                        LEFT JOIN attendances att ON att.emp_id = ? AND att.date = ?
                        LEFT JOIN shift_types st ON st.id = ?
                        LEFT JOIN employeeshiftdetails esd 
                            ON esd.emp_id = ? AND ? BETWEEN esd.date_from AND esd.date_to
                        LEFT JOIN shift_types esd_shift ON esd.shift_id = esd_shift.id
                        LEFT JOIN employee_late_attendance_minites la 
                            ON la.emp_id = ? AND la.attendance_date = ?
                        LEFT JOIN (
                            SELECT ot.emp_id, ot.date, ot.hours, ot.double_hours, ot.triple_hours, 
                                ot.holiday_normal_hours
                            FROM ot_approved ot
                        ) ot ON ot.emp_id = ? AND ot.date = ?
                        LEFT JOIN (
                            SELECT l.emp_id, lt.leave_type AS leavename, l.no_of_days, l.leave_from, l.leave_to
                            FROM leaves l
                            LEFT JOIN leave_types lt ON l.leave_type = lt.id
                            WHERE l.status = 'Approved'
                        ) leave_data ON leave_data.emp_id = ? AND ? BETWEEN leave_data.leave_from AND leave_data.leave_to
                        LEFT JOIN holidays h ON h.date = ?
                        GROUP BY dr.date
                    ", [
                        $date, $date, $date, $date, // For date formatting and weekday check
                        $date, // For the dr alias
                        $employee->emp_id, $date, // For attendance join
                        $employee->emp_shift, // For shift_types join
                        $employee->emp_id, $date, // For employeeshiftdetails join
                        $employee->emp_id, $date, // For late attendance join
                        $employee->emp_id, $date, // For OT join
                        $employee->emp_id, $date, // For leave join
                        $date, // For holiday join
                    ]);

                    if($record){
                        $work_days++;
                        $leave_days += $record[0]->leave_days;
                        $no_pay_days += ($record[0]->leave_type == 'No Pay Leave' ? $record[0]->leave_days : 0);
                        $normal_ot_hours += $record[0]->ot_hours;
                        $double_ot_hours += $record[0]->double_ot;
                    }

                    // Add the record (we take the first result since we're querying one date at a time)
                    $attendanceRecords[] = $record[0] ?? [
                        'in_date' => $date,
                        'out_date' => $date,
                        'day_type' => '',
                        'shift' => '',
                        'in_time' => '',
                        'out_time' => '',
                        'late_min' => 0,
                        'leave_type' => '',
                        'leave_days' => 0,
                        'ot_hours' => 0,
                        'double_ot' => 0,
                        'triple_ot' => 0
                    ];


                }

                if($work_days > 0){
                    $extraData = $this->calculateExtraMetrics($employee->id,$work_days,$leave_days,$no_pay_days,$normal_ot_hours, $double_ot_hours);
                }
                else{
                    $extraData = [];
                }

                $salaryadvance = DB::table('salary_advances')
                    ->select('id', 'emp_id', 'date', 'paid_amount', 'remark') // Pass them as separate parameters
                    ->where('emp_id', $employee->emp_id)
                    ->get();
                
                // Store Employee Data
                $employeeData[] = [
                    'id' => $employee->id,
                    'emp_id' => $employee->emp_id,
                    'emp_etfno' => $employee->emp_etfno,
                    'emp_fullname' => $employee->emp_fullname,
                    'jobtitlename' => $employee->jobtitlename,
                    'departmentname' => $employee->departmentname,
                    'companyname' => $employee->companyname,
                    'emp_gender' => $employee->emp_gender,
                    'shiftname' => $employee->shiftname,
                    'attendance' => $attendanceRecords,
                    'extra' => $extraData,
                    'salaryadvance' => $salaryadvance
                ];
            }
        }
        
        $html = view('Report.estimate_salary_report_table', compact('employeeData'))->render();

        return $html;
    }

    public function calculateExtraMetrics($empid,$work_days,$leave_days,$no_pay_days,$normal_ot_hours, $double_ot_hours){
        $emp_etfno = $empid;
        $emp_work=$work_days;
        $emp_leave=$leave_days;
        $emp_nopay=$no_pay_days; 
        $emp_ot_i=$normal_ot_hours; 
        $emp_ot_ii= $double_ot_hours;
        
        $sql_info = "SELECT payroll_profiles.id as payroll_profile_id, payroll_profiles.basic_salary, payroll_profiles.day_salary, payroll_process_types.pay_per_day FROM `payroll_profiles` inner join payroll_process_types on payroll_profiles.payroll_process_type_id=payroll_process_types.id WHERE payroll_profiles.emp_id=?";
        $profiles = DB::select($sql_info, [$emp_etfno]);
        
        $employeePayslip = EmployeePayslip::where(['payroll_profile_id' => $profiles[0]->payroll_profile_id])
            ->latest()
            ->first();
            
        $emp_payslip_no = empty($employeePayslip) ? 1 : ($employeePayslip->emp_payslip_no + 1);

        $empjobcategoryinfo = DB::table('employees')
            ->leftJoin('job_categories', 'job_categories.id' , '=', 'employees.job_category_id')
            ->select('job_categories.emp_payroll_workdays', 'job_categories.emp_payroll_workhrs')
            ->where('employees.id', $empid)
            ->first();
        
        
        
        /**/
        // DB::enableQueryLog();
        $sql_main="SELECT fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_pssc FROM (SELECT drv_figs.fig_name, drv_figs.fig_group, drv_figs.fig_group_title, drv_figs.fig_value AS fig_base_ratio, COALESCE(NULLIF(drv_figs.fig_value*(((drv_figs.fig_group='FIXED') * ? * ?) + ((drv_figs.fig_group='FIXED') * (1 - ?)) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.nopay_payable * (drv_figs.fig_group='BASIC')) + (? * (drv_figs.fig_group='OTHRS1')) + (? * (drv_figs.fig_group='OTHRS2')))*drv_figs.pay_per_day, 0), (drv_figs.fig_value*drv_figs.fig_revise)) AS fig_value, drv_figs.fig_hidden, drv_figs.epf_payable, drv_figs.remuneration_pssc FROM (SELECT 'Basic' AS fig_name, 'BASIC' AS fig_group, 'BASIC' AS fig_group_title, COALESCE(NULLIF(CAST(?*? AS DECIMAL(10,2)), 0), ?) AS fig_value, ? AS pay_per_day, 1 AS fig_revise, 0 AS fig_hidden, 1 AS epf_payable, 1 AS work_payable, 1 AS nopay_payable, 'BASIC' AS remuneration_pssc UNION ALL SELECT 'No pay' AS fig_name, 'BASIC' AS fig_group, 'NOPAY' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 1 AS nopay_payable, 'NOPAY' AS remuneration_pssc UNION ALL SELECT 'Normal OT' AS fig_name, 'OTHRS1' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS1' AS remuneration_pssc UNION ALL SELECT 'Double OT' AS fig_name, 'OTHRS2' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS2' AS remuneration_pssc UNION ALL select drv_allfacility.remuneration_name AS fig_name, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (IFNULL(drv_dayfacility.pre_eligible_amount, drv_empfacility.new_eligible_amount)*drv_allfacility.value_group) AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, pre_eligible_amount, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ? BETWEEN min_days AND max_days) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id) AS drv_figs UNION ALL SELECT drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, drv_docs.fig_value AS fig_base_ratio, drv_docs.fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc FROM (SELECT remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id) AS drv_docs) AS drv_main";
        $employee = DB::select($sql_main, [$emp_work, $profiles[0]->pay_per_day, $profiles[0]->pay_per_day, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii, $profiles[0]->day_salary, $profiles[0]->pay_per_day, $profiles[0]->basic_salary, $profiles[0]->pay_per_day, ($profiles[0]->day_salary*-1), ($profiles[0]->day_salary/8), (($profiles[0]->day_salary*1)/8), $profiles[0]->payroll_profile_id, $emp_work, $profiles[0]->payroll_profile_id, $emp_payslip_no]);
        // $queryLog = DB::getQueryLog();
        // $query = end($queryLog); // Get the last executed query

        // // Replace bindings in the query
        // $sql = vsprintf(str_replace('?', "'%s'", $query['query']), $query['bindings']);

        // dd($sql);
        
        $figs_list = array();
        $epf_payable_tot = 0;
        
        foreach($employee as $r){
            if($r->epf_payable){
                $epf_payable_tot += $r->fig_value;
            }
            
            if(!isset($figs_list[$r->remuneration_pssc])){
                $figs_list[$r->remuneration_pssc]=array(
                                        'fig_grp_title'=>$r->fig_group_title, 
                                        'fig_val'=>0, 
                                        'fig_base_rate'=>$r->fig_base_ratio
                                    );
            }
            
            $figs_list[$r->remuneration_pssc]['fig_val'] += $r->fig_value;
        }


        
        $payperiod_workdays=$empjobcategoryinfo->emp_payroll_workdays; $payperiod_holidays=0;
        $payperiod_netdays=($payperiod_workdays-$payperiod_holidays)*-1;
        
        $reg_keys = array('NOPAY', 'OTHRS1', 'OTHRS2');
        $reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays), 
                            'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>$empjobcategoryinfo->emp_payroll_workhrs), 
                            'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>$empjobcategoryinfo->emp_payroll_workhrs)
                    );
        
        foreach($figs_list as $k=>$v){
            if(in_array($k, $reg_keys)){
                $units_tot = ($figs_list[$k]['fig_base_rate'] != 0) ? ($figs_list[$k]['fig_val'] / $figs_list[$k]['fig_base_rate']) : 0;
                // $new_base_rate = (($epf_payable_tot*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);

                // Check if key_param is set and not equal to 0 before dividing
                $keyParam = $reg_cols[$k]['key_param'] ?? 0;
                
                if ($keyParam != 0) {
                    $new_base_rate = (($epf_payable_tot * $reg_cols[$k]['fig_premium']) / $keyParam);
                } else {
                    $new_base_rate = 0; // Don't divide, set base rate to 0
                }
                $figs_list[$k]['fig_val']=number_format((float)($new_base_rate*$units_tot), 2, '.', '');
                $figs_list[$k]['fig_base_rate']=number_format((float)$new_base_rate, 2, '.', '');
            }
        }
        
        // dd(['nopay_val' => $figs_list['NOPAY']['fig_val']]);
        //return ['nopay_val' => $figs_list['NOPAY']['fig_val'], 'nopay_base_rate' => $figs_list['NOPAY']['fig_base_rate']];
        return [
                    'emp_auto_id' => $empid,
                    'basic_val' => $figs_list['BASIC']['fig_val'],
                    'basic_base_rate' => $figs_list['BASIC']['fig_base_rate'],
                    'nopay_val' => $figs_list['NOPAY']['fig_val'],
                    'nopay_base_rate' => $figs_list['NOPAY']['fig_base_rate'],
                    'othrs1_val' => $figs_list['OTHRS1']['fig_val'],
                    'othrs1_base_rate' => $figs_list['OTHRS1']['fig_base_rate'],
                    'othrs2_val' => $figs_list['OTHRS2']['fig_val'],
                    'othrs2_base_rate' => $figs_list['OTHRS2']['fig_base_rate']
                ];
		
	}
}