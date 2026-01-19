<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MealRequest;
use App\MealType;
use DB;
use Auth;
use Carbon\Carbon;

class MealrecivedController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('meal_recevied-list');
        if (!$permission) {
            abort(403);
        }

         $meal_types = DB::table('meal_types')
            ->select('id', 'meal_name')
            ->where('status', '1')
            ->get();

        return view('Meal_management.mealrecived_mark',compact('meal_types'));
    }

     public function markedmealreceving(Request $request)
    {

        $permission = \Auth::user()->can('meal_recevied-create');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('records');
        $recivetype = $request->input('recivetype');
        
        $current_date_time = Carbon::now()->toDateTimeString();

         foreach ($dataarry as $row) {
            $id = $row['id'];

            $data = array(
                'received_status' => $recivetype,
                'updated_at' => $current_date_time,
            );
        
            MealRequest::where('id', $id)
            ->update($data);
        }

        return response()->json(['success' => 'Meal Receiving is successfully Marked']);
    }

     public function finalaaprovel()
    {
        $user = auth()->user();
        $permission = $user->can('final_meal_approve-list');
        if (!$permission) {
            abort(403);
        }

         $meal_types = DB::table('meal_types')
            ->select('id', 'meal_name')
            ->where('status', '1')
            ->get();

      $remunerations=DB::table('remunerations')->select('*')->where('remuneration_type', 'Deduction')->get();
        return view('Meal_management.finalmealapprovel',compact('meal_types','remunerations'));
    }
    
     public function generatemealdeduction(Request $request)
    {

        $user = Auth::user();
        $permission = $user->can('final_meal_approve-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $mealtype = $request->get('mealtype');
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

        if (!empty($mealtype)) {
            $query->where('mr.meal_type', $mealtype);
        }

        $query->groupBy('mr.emp_id');

        $results = $query->get();

         foreach ($results as $record) {

            $employeeObj = (object)[
                'emp_id' => $record->emp_id,
                'emp_name_with_initial' => $record->emp_name_with_initial,
                'calling_name' => $record->calling_name
            ];

            $mealDetails = DB::table('meal_types')
                ->select('id', 'meal_rate', 'penalty_rate')
                ->where('status', 1);

            if (!empty($mealtype)) {
                $mealDetails->where('id', $mealtype);
            }
            
            $mealDetails = $mealDetails->get(); 


            $totalTakenCount = 0;
            $totalNotTakenCount = 0;
            $totalDeduction = 0;


             foreach ($mealDetails as $meallist) {

                $penelty =  $meallist->penalty_rate;

               $takencount = DB::table('meal_requests')
                    ->where('status', 1)
                    ->where('meal_type', $meallist->id)
                    ->where('emp_id', $record->emp_id)
                    ->where('received_status', 1)
                    ->count();

               $nottaken = DB::table('meal_requests')
                    ->where('status', 1)
                    ->where('meal_type', $meallist->id)
                    ->where('emp_id', $record->emp_id)
                    ->where('received_status', 2)
                    ->count();

                $deduction =  $nottaken * $penelty;

                $totalTakenCount += $takencount;
                $totalNotTakenCount += $nottaken;
                $totalDeduction += $deduction;
             }

             if( $totalDeduction > 0){
                $data[] = [
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' =>EmployeeHelper::getDisplayName($employeeObj),
                    'emp_autoid' => $record->emp_auto_id,
                    'total_taken_count' => $totalTakenCount,
                    'total_not_taken_count' => $totalNotTakenCount,
                    'total_deduction' => number_format($totalDeduction, 2, '.', ''),
                ];
             }

         }

        return response()->json(['data' => $data ?? []]);
    }


    public function approvemealdeduction(Request $request)
    {

    $permission = \Auth::user()->can('final_meal_approve-create');
    if (!$permission) {
        abort(403);
    }

        $dataarry = $request->input('records');
        $remunitiontype = $request->input('remunitiontype');
    
    $current_date_time = Carbon::now()->toDateTimeString();

    foreach ($dataarry as $row) {

        $empid = $row['empid'];
        $empname = $row['emp_name'];
        $total_taken = str_replace([','], '', $row['total_taken']);
        $total_not_taken = str_replace([','], '', $row['total_not_taken']);
        $total_deduction = str_replace([','], '', $row['total_deduction']);
        $autoid = $row['autoid'];

        $profiles = DB::table('payroll_profiles')
        ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
        // ->where('payroll_profiles.emp_etfno', $empid)
        ->where('payroll_profiles.emp_id', $autoid)
        ->select('payroll_profiles.id as payroll_profile_id')
        ->first();

    if ($profiles) {


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
                'payment_amount' => $total_deduction,
                'payment_cancel' => '0',
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time
            ]);
        }
        else{
            $termpayment = new EmployeeTermPayment();
            $termpayment->remuneration_id = $remunitiontype;
            $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
            $termpayment->emp_payslip_no = $newpaylispno;
            $termpayment->payment_amount = $total_deduction;
            $termpayment->payment_cancel = 0;
            $termpayment->created_by = Auth::id();
            $termpayment->created_at = $current_date_time;
            $termpayment->save(); 
        }
    }else{
        continue;
    }

    }

    return response()->json(['success' => 'Meal Deduction is successfully Approved']);
    }

}
