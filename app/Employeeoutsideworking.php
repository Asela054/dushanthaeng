<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employeeoutsideworking extends Model
{
     protected $table = 'employee_outside_working';
     protected $fillable = [
        'emp_id',
        'date',
        'location',
        'remark',
        'amount',
        'status',
    ];
}
