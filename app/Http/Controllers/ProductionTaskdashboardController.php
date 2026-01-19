<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductionTaskdashboardController extends Controller
{
    public function index()
    {

        return view('Dashboard.productiontask');
    }
     public function meanmanagement()
    {

        return view('Dashboard.meal');
    }
}
