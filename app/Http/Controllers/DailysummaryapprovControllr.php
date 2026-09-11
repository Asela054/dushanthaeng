<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Auth;

class DailysummaryapprovControllr extends Controller
{
     public function index()
    {
        $user = Auth::user();
        $permission = $user->can('Daily-Summary-Approvals');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $leave_types = DB::table('leave_types')->get();
        return view('Daily_summary.daily_approve', compact('leave_types'));
    }
}
