<?php

namespace App\Http\Controllers\OutsideEmployees;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\OutsideEmployees\OutsideEmployee;
use Auth;
use Carbon\Carbon;
use Datatables;

class OutsideEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('employee-list');

        if(!$permission) {
            abort(403);
        }

        return view('OutsideEmployees.outsideEmployees');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('employee-create');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $advance = new OutsideEmployee;
        $advance->emp_id = $request->input('employee');
        $advance->amount = $request->input('amount');
        $advance->status = '1'; 
        $advance->created_at = Carbon::now()->toDateTimeString();

        $advance->save();

        return response()->json(['success' => 'Outside Employee Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('employee-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('outside_employees')
                ->leftjoin('employees', 'outside_employees.emp_id', '=', 'employees.emp_id')
                ->select(
                    'outside_employees.*', 
                    'employees.emp_name_with_initial as employee_name'
                )
                ->where('outside_employees.id', $id)
                ->first();
            
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, OutsideEmployee $advance)
    {
        $user = auth()->user();
        $permission = $user->can('employee-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'emp_id' => $request->employee,
            'amount' => $request->amount,
            'updated_at' => Carbon::now()->toDateTimeString()
        );

        OutsideEmployee::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Outside Employee is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('employee-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = OutsideEmployee::findOrFail($id);
        $data->status = 3;
        $data->save();
        
        return response()->json(['success' => 'Deleted successfully']);
    }
}
