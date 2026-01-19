<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MealRequest;
use App\MealType;
use DB;
use Auth;
use Carbon\Carbon;

class MealrequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('meal_request-list');
        if (!$permission) {
            abort(403);
        }

         $meal_types = DB::table('meal_types')
            ->select('id', 'meal_name')
            ->where('status', '1')
            ->get();

        return view('Meal_management.meal_request',compact('meal_types'));
    }

     public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('meal_request-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

            $date = $request->input('date');
            $mealtype = $request->input('mealtype');
            $allocatetype = $request->input('allocatetype');
            $tableData = $request->input('tableData');


                foreach ($tableData as $rowtabledata) {
                $emp_id = $rowtabledata['col_1'];
                $empname = $rowtabledata['col_2'];

                $EmpProductAllocationDetail = new MealRequest();
                $EmpProductAllocationDetail->emp_id = $emp_id;
                $EmpProductAllocationDetail->date = $date;
                $EmpProductAllocationDetail->meal_type = $mealtype;
                $EmpProductAllocationDetail->issue_type = $allocatetype;
                $EmpProductAllocationDetail->received_status = '0';
                $EmpProductAllocationDetail->status = '1';
                $EmpProductAllocationDetail->approve_status = '1';
                $EmpProductAllocationDetail->save();
            }

            return response()->json(['success' => 'Employee Meal Request Successfully Inserted']);
    }

     public function edit(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('meal_request-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $id = $request->input('id');
        if (request()->ajax()){
            $data = DB::table('meal_requests')
                 ->leftJoin('employees as e', 'meal_requests.emp_id', '=', 'e.emp_id')
                ->select('meal_requests.*','e.emp_name_with_initial as emp_name_with_initial','e.calling_name as calling_name')
                ->where('meal_requests.id', $id)
                ->first(); 
            return response()->json(['result' => $data]);
        }
    }

       public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('meal_request-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $id = $request->input('hidden_id');
        $date = $request->input('date');
        $mealtype = $request->input('mealtype');
        $allocatetype = $request->input('allocatetype');

        $mealRequest = MealRequest::findOrFail($id);
        $mealRequest->date = $date;
        $mealRequest->meal_type = $mealtype;
        $mealRequest->issue_type = $allocatetype;
        $mealRequest->updated_at = Carbon::now()->toDateTimeString();
        $mealRequest->save();

        return response()->json(['success' => 'Employee Meal Request Successfully Updated']);
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('meal_request-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        
        $id = $request->input('id');

        $mealRequest = MealRequest::findOrFail($id);
        $mealRequest->status = '3';
        $mealRequest->updated_at = Carbon::now()->toDateTimeString();
        $mealRequest->save();

        return response()->json(['success' => 'Employee Meal Request Successfully Deleted']);
    }
}
