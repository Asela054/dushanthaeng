<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mealallowancededuction;
use App\MealRequest;
use App\MealType;
use DB;
use Auth;
use Carbon\Carbon;


class MealattendanceController extends Controller
{
      public function index()
    {
         $user = auth()->user();
            $permission = $user->can('final_meal_approve-list');
            if (!$permission) {
                abort(403);
            }

        $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Addition')->get();
        return view('Meal_management.attendancemealapprovel',compact('remunerations'));
    }

    public function generateattendancemealdeduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('final_meal_approve-create');

        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');


        $query = DB::table('meal_requests as mr')
            ->select(
                'mr.emp_id',
                'e.emp_name_with_initial',
                'e.calling_name',
                'e.emp_id as employee_emp_id',
                'e.id as emp_auto_id'
            )
            ->leftJoin('employees as e', 'mr.emp_id', '=', 'e.emp_id')
            ->where('mr.status', 1)
            ->whereBetween('mr.date', [$from_date, $to_date]);

        if (!empty($department)) {
            $query->where('e.emp_department', $department);
        }

        if (!empty($employee)) {
            $query->where('mr.emp_id', $employee);
        }

        $query->groupBy('mr.emp_id');

        $results = $query->get();
        $data = [];

        foreach ($results as $record) {
            $employeeObj = (object)[
                'emp_id' => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'calling_name' => $record->calling_name
            ];

            $mealDetailsQuery = DB::table('meal_types')
                ->select('id', 'meal_rate', 'penalty_rate')
                ->where('status', 1);
            $mealDetails = $mealDetailsQuery->get();

            $totalTakenCount = 0;
            $totalNotTakenCount = 0;
            $totalDeduction = 0;
            $totalAllowance = 0;
            $totalBalance = 0;
            $totalpenelty = 0;

            // Calculate attendance days
            $attendanceDays = DB::table('attendances')
                ->where('emp_id', $record->emp_id)
                ->whereBetween('date', [$from_date, $to_date])
                ->distinct('date')
                ->count('date');

            $needsHalfDeduction = ($attendanceDays < 24);

            foreach ($mealDetails as $meallist) {

                $penaltyRate = $meallist->penalty_rate;
                $mealRate = $meallist->meal_rate;

                // Get taken meals count
                $takenCount = DB::table('meal_requests')
                    ->where('status', 1)
                    ->where('meal_type', $meallist->id)
                    ->where('emp_id', $record->emp_id)
                    ->where('received_status', 1)
                    ->whereBetween('date', [$from_date, $to_date])
                    ->count();

                // Get not taken meals count
                $notTakenCount = DB::table('meal_requests')
                    ->where('status', 1)
                    ->where('meal_type', $meallist->id)
                    ->where('emp_id', $record->emp_id)
                    ->where('received_status', 2)
                    ->whereBetween('date', [$from_date, $to_date])
                    ->count();

                // Calculate penalty deduction for not taken meals
                $penaltyDeduction = $notTakenCount * $penaltyRate;
                
                // Calculate total allowance for taken meals
                $mealAllowance = $takenCount * $mealRate;

                $totalTakenCount += $takenCount;
                $totalNotTakenCount += $notTakenCount;
                $totalpenelty += $penaltyDeduction;
                $totalAllowance += $mealAllowance;
            }

            // Apply 50% deduction from meal allowance if employee worked less than 24 days
            $attendanceDeduction = 0;
            if ($needsHalfDeduction && $totalAllowance > 0) {
                $attendanceDeduction = $totalAllowance * 0.5;
            }

            // Total deduction = penalty deduction + attendance deduction
            $totalDeduction += $attendanceDeduction;

          $totalBalance = max(0, $totalAllowance - $totalDeduction);


            if ($totalAllowance > 0) {
                $data[] = [
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' => EmployeeHelper::getDisplayName($employeeObj),
                    'emp_autoid' => $record->emp_auto_id,
                    'total_taken_count' => $totalTakenCount,
                    'total_not_taken_count' => $totalNotTakenCount,
                    'attendance_days' => $attendanceDays,
                    'needs_half_deduction' => $needsHalfDeduction,
                    'meal_allowance' => number_format($totalAllowance, 2, '.', ''),
                    'penalty_deduction' => number_format($totalpenelty, 2, '.', ''),
                    'attendance_deduction' => number_format($attendanceDeduction, 2, '.', ''),
                    'total_deduction' => number_format($totalDeduction, 2, '.', ''),
                    'total_balance' => number_format($totalBalance, 2, '.', ''),
                ];
            }
        }

        return response()->json(['data' => $data]);
    }

    public function approvemealdeduction(Request $request)
    {

        $permission = \Auth::user()->can('final_meal_approve-create');
        if (!$permission) {
            abort(403);
        }

            $dataarry = $request->input('records');
            $remunitiontype = $request->input('remunitiontype');
            $from_date = $request->input('from_date');
            $to_date = $request->input('to_date');
        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $total_taken = str_replace([','], '', $row['total_taken']);
            $meal_allowance = str_replace([','], '', $row['meal_allowance']);
            $total_not_taken = str_replace([','], '', $row['total_not_taken']);
            $penalty_deduction = str_replace([','], '', $row['penalty_deduction']);
            $attendance_days = str_replace([','], '', $row['attendance_days']);
            $attendance_deduction = str_replace([','], '', $row['attendance_deduction']);
            $total_deduction = str_replace([','], '', $row['total_deduction']);
            $total_balance = str_replace([','], '', $row['total_balance']);
            $autoid = $row['autoid'];

            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
            // ->where('payroll_profiles.emp_etfno', $empid)
            ->where('payroll_profiles.emp_id', $autoid)
            ->select('payroll_profiles.id as payroll_profile_id')
            ->first();

        if ($profiles) {

            $allowance = DB::table('meal_allowances_deduction')
                ->where('emp_id', $empid)
                ->where('remunition_type', $remunitiontype)
                ->whereBetween('from_date', [$from_date, $to_date]) 
                ->whereBetween('to_date', [$from_date, $to_date])  
                ->first();

                if($allowance){

                     DB::table('meal_allowances_deduction')
                    ->where('emp_id', $empid)
                    ->where('from_date', [$from_date, $to_date]) 
                    ->where('to_date', [$from_date, $to_date])  
                    ->update([
                        'remunition_type' => $remunitiontype,
                        'total_taken' => $total_taken,
                        'allowance' => $meal_allowance,
                        'not_taken' => $total_not_taken,
                        'penalty_deduction' => $penalty_deduction,
                        'attendance_days' => $attendance_days,
                        'attendance_deduction' => $attendance_deduction,
                        'total_deduction' => $total_deduction,
                        'balance' => $total_balance,
                        'updated_at' => $current_date_time
                    ]);

                }else{

                    $approvedmeal = new Mealallowancededuction();
                    $approvedmeal->emp_id = $empid;
                    $approvedmeal->from_date = $from_date;
                    $approvedmeal->to_date = $to_date;
                    $approvedmeal->remunition_type = $remunitiontype;
                    $approvedmeal->total_taken = $total_taken;
                    $approvedmeal->allowance = $meal_allowance;
                    $approvedmeal->not_taken = $total_not_taken;
                    $approvedmeal->penalty_deduction = $penalty_deduction;
                    $approvedmeal->attendance_days = $attendance_days;
                    $approvedmeal->attendance_deduction = $attendance_deduction;
                    $approvedmeal->total_deduction = $total_deduction;
                    $approvedmeal->balance = $total_balance;
                    $approvedmeal->created_at = $current_date_time;
                    $approvedmeal->save();
                }

                 $paysliplast = DB::table('employee_payslips')
                            ->select('emp_payslip_no')
                            ->where('payroll_profile_id', $profiles->payroll_profile_id)
                            ->where('payslip_cancel', 0)
                            ->orderBy('id', 'desc')
                            ->first();

                        if ($paysliplast) {
                            $emp_payslipno = $paysliplast->emp_payslip_no;
                            $newpaylispno =  $emp_payslipno +1;
                        }else{
                            $newpaylispno = 1;
                        }
                    
                        $termpaymentcheck = DB::table('employee_term_payments')
                            ->select('id')
                            ->where('payroll_profile_id', $profiles->payroll_profile_id)
                            ->where('emp_payslip_no', $newpaylispno)
                            ->where('remuneration_id', $remunitiontype)
                            ->first();
                        
                        if($termpaymentcheck){
                            DB::table('employee_term_payments')
                            ->where('id', $termpaymentcheck->id)
                            ->update([
                                'payment_amount' => $total_balance,
                                'payment_cancel' => '0',
                                'updated_by' => Auth::id(),
                                'updated_at' => $current_date_time
                            ]);
                        }else{
                             $termpayment = new EmployeeTermPayment();
                            $termpayment->remuneration_id = $remunitiontype;
                            $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                            $termpayment->emp_payslip_no = $newpaylispno;
                            $termpayment->payment_amount = $total_balance;
                            $termpayment->payment_cancel = 0;
                            $termpayment->created_by = Auth::id();
                            $termpayment->created_at = $current_date_time;
                            $termpayment->save(); 
                        }
        }else{
            continue;
        }

        }

       return response()->json(['success' => 'Meal Allowance is successfully Approved']);
    }

}
