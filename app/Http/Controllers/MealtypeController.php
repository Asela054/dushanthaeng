<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\MealType;

class MealtypeController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('meal_type-list');
        if (!$permission) {
            abort(403);
        }

        return view('Meal_management.mealtype');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('meal_type-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'mealtype'    =>  'required',
            'mealrate' => 'required',
            'penaltyrate' => 'required'
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $machine=new MealType;
        $machine->meal_name=$request->input('mealtype');
        $machine->meal_rate=$request->input('mealrate');
        $machine->penalty_rate=$request->input('penaltyrate');    
        $machine->status=1;
        $machine->save();

        return response()->json(['success' => 'Meal Added Successfully.']);
    }

     public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('meal_type-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = MealType::findOrFail($id);
            $result = $data->toArray();
            
            return response()->json(['result' => $result]);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('meal_type-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'mealtype'    =>  'required',
            'mealrate' => 'required',
            'penaltyrate' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }


        $form_data = array(
            'meal_name'     =>  $request->mealtype,
            'meal_rate'      =>  $request->mealrate,
            'penalty_rate'    =>  $request->penaltyrate
        );

        MealType::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

     public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('meal_type-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = MealType::findOrFail($id);
        $data->status=3;
        $data->save();

        return response()->json(['success' => 'Meal Type Deleted Successfully.']);
    }


}
