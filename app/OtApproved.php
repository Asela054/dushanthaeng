<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OtApproved extends Model
{
    protected $table = 'ot_approved';
    protected $fillable = [
        'emp_id',
        'date',
        'from',
        'to',
        'hours',
        //'one_point_five_hours',
        'double_hours',
        'is_holiday',
        'created_at',
        'created_by'];

    public function get_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('hours');
        return $ot_hours;
    }

    public function get_double_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $double_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('double_hours');
        return $double_ot_hours;
    }

    public function get_triple_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $triple_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('triple_hours');
        return $triple_ot_hours;
    }

    public function is_exists_in_ot_approved($emp_id, $date, $OTfrom){
        $date = Carbon::parse($date);
        $date = $date->format('Y-m-d');
        $ot = OtApproved::where('emp_id', $emp_id)
            ->where('date', '=', $date)
            ->where('from', '=', $OTfrom)
            ->get();

        $status = true;
        if($ot->isEmpty()){
            $status = false;
        }

        return $status;
    }

    

     public function get_holiday_double_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $holiday_double_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('holiday_double_hours');
        return $holiday_double_ot_hours;
    }

     public function get_holiday_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $holiday_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('holiday_normal_hours');
        return $holiday_ot_hours;
    }

    public function get_sundaywork_days_monthly($emp_id, $month ,$closedate)
    {
        $sundaywork_days = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('sunday_work_days');
        return $sundaywork_days;
    }

    public function get_poyawork_days_monthly($emp_id, $month ,$closedate)
    {
        $poyawork_days = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('poya_work_days');
        return $poyawork_days;
    }

    public function get_mercantilework_days_monthly($emp_id, $month ,$closedate)
    {
        $mercantilework_days = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('mercantile_work_days');
        return $mercantilework_days;
    }

    public function get_sundaydouble_ot_hours_monthly($emp_id, $month ,$closedate)
    {
        $sundaydouble_ot_hours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('sunday_double_ot_hrs');
        return $sundaydouble_ot_hours;
    }

    public function get_poyaextended_normal_othours_monthly($emp_id, $month ,$closedate)
    {
        $poyaextended_normal_othours = OtApproved::where('emp_id', $emp_id)
                            ->where('date', 'like', $month.'%')
                            ->where('date', '<=', $closedate)
                            ->sum('poya_extended_normal_ot_hrs');
        return $poyaextended_normal_othours;
    }

}
