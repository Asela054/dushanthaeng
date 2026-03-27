<?php

namespace App\Http\Controllers;

use App\EmployeeTermPayment;
use App\Employeeoutsideworking;
use App\Helpers\EmployeeHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeoutsideworkingController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('outside_working-list');

        if(!$permission) {
            abort(403);
        }
        return view('outside_working.employee_outsideworking');
    }

      public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('outside_working-create');
        if(!$permission) {
           return response()->json(['error' => 'Unauthorized'], 403);
        }


        $outsideworking = new Employeeoutsideworking;
        $outsideworking->emp_id = $request->input('employee');
        $outsideworking->date = $request->input('date');
        $outsideworking->location = $request->input('location');
        $outsideworking->remark = $request->input('remark');
        $outsideworking->status = 1;
        $outsideworking->created_at = Carbon::now()->toDateTimeString();
        $outsideworking->save();

        return response()->json(['success' => 'Outside Working Added successfully.']);
    }

       public function edit(Request $request)
    {
        $id = $request ->id;

        $user = auth()->user();
        $permission = $user->can('outside_working-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = Employeeoutsideworking::leftJoin('employees', 'employee_outside_working.emp_id', '=', 'employees.emp_id')
                ->where('employee_outside_working.id', $id)
                ->select('employee_outside_working.*', 'employees.emp_name_with_initial')
                ->first();
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('outside_working-edit');
        if(!$permission) {
              return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'emp_id' => $request->employee,
            'date' => $request->date,
            'location' => $request->location,
            'remark' => $request->remark,
            'updated_at' => Carbon::now()->toDateTimeString(),
        );

        Employeeoutsideworking::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Outside Working is successfully updated']);
    }

      public function destroy($id)
    {
        $permission = \Auth::user()->can('outside_working-delete');
        if (!$permission) {
            abort(403);
        }
        $form_data = array(
            'status' =>  '3'
        );
        Employeeoutsideworking::where('id',$id)
        ->update($form_data);

        return response()->json(['success' => 'Outside Working is Successfully Deleted']);
    }


    // Outside Working Final Approval


    public function outsideworking_approvel(){

        $user = Auth::user();
        $permission = $user->can('outside_working_approve-list');
        if(!$permission){
            abort(403);
        }

        $remunerations=DB::table('remunerations')->select('*')->get();

        return view('outside_working.employee_outsideworkingapprovel',compact('remunerations'));
    }


     public function generateoutsideworkingallowance(Request $request){
         $user = Auth::user();
        $permission = $user->can('outside_working_approve-create');

        if(!$permission){
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = [];
        
        $employee = $request->get('employee');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');

          $query = DB::query()
            ->select('employees.id as emp_auto_id',
                'employees.calling_name',
                'employees.emp_id',
                'employees.emp_name_with_initial'            
            )
            ->from('employees as employees');
        if ($employee != '') {
            $query->where(['employees.emp_id' => $employee]);
        }
        $query->where('employees.deleted', 0);
        $query->where('employees.is_resigned',0);
        $query->groupBy('employees.emp_id');
        $results = $query->get();

         foreach ($results as $record) {

            $employeeObj = (object)[
            'emp_id' => $record->emp_id,
            'emp_name_with_initial' => $record->emp_name_with_initial,
            'calling_name' => $record->calling_name
        ];

        $outsideWorkCount = DB::table('employee_outside_working')
            ->where('emp_id', $record->emp_id)
            ->where('status', 1)
            ->whereBetween('date', [$from_date, $to_date])
            ->count();

                
              // Only add to array if there are visits to outside locations
                if ($outsideWorkCount > 0) {
                    // Get the allowance amount
                    $allowanceConfig = DB::table('amount_configuration')
                        ->where('id', 1)
                        ->first();
                    
                    $allowanceAmount = $allowanceConfig ? $allowanceConfig->pay_amount : 0;
                    $totalAllowance = $outsideWorkCount * $allowanceAmount;
                    
                    $data[] = [
                        'emp_auto_id' => $record->emp_auto_id,
                        'emp_id' => $record->emp_id,
                        'emp_name_with_initial' =>EmployeeHelper::getDisplayName($employeeObj),
                        'day_count' => $outsideWorkCount,
                        'allowance_amount' => $allowanceAmount,
                        'total_allowance' => $totalAllowance
                    ];
                }
         }
         return response()->json(['data' => $data]);
    }


     public function approveoutsideworkingallowance(Request $request)
    {

        $permission = \Auth::user()->can('outside_working_approve-create');
        if (!$permission) {
            abort(403);
        }

        $dataarry = $request->input('dataarry');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
         $remunerationid = $request->input('remunitiontype');

        
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($dataarry as $row) {

            $empid = $row['empid'];
            $empname = $row['emp_name'];
            $dayscount = $row['dayscount'];
            $rate = $row['rate'];
            $allowance_amount =$row['total_amount'];
            $autoid = $row['autoid'];

            $profiles = DB::table('payroll_profiles')
            ->join('payroll_process_types', 'payroll_profiles.payroll_process_type_id', '=', 'payroll_process_types.id')
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



             if($allowance_amount != 0){

                $termpaymentcheck = DB::table('employee_term_payments')
                ->select('id')
                ->where('payroll_profile_id', $profiles->payroll_profile_id)
                ->where('emp_payslip_no', $newpaylispno)
                ->where('remuneration_id', $remunerationid)
                ->first();
            
                if($termpaymentcheck){
                    DB::table('employee_term_payments')
                    ->where('id', $termpaymentcheck->id)
                    ->update([
                        'payment_amount' => $allowance_amount,
                        'payment_cancel' => '0',
                        'updated_by' => Auth::id(),
                        'updated_at' => $current_date_time
                    ]);
                }
                else{
                    $termpayment = new EmployeeTermPayment();
                    $termpayment->remuneration_id = $remunerationid;
                    $termpayment->payroll_profile_id = $profiles->payroll_profile_id;
                    $termpayment->emp_payslip_no = $newpaylispno;
                    $termpayment->payment_amount = $allowance_amount;
                    $termpayment->payment_cancel = 0;
                    $termpayment->created_by = Auth::id();
                    $termpayment->created_at = $current_date_time;
                    $termpayment->save(); 
                }
            }
            
        }
        else{
            continue;
        }

        }

        return response()->json(['success' => 'Outside Working Insentive is successfully Approved']);
    }

}
