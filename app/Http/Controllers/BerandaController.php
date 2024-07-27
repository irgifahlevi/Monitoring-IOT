<?php

namespace App\Http\Controllers;

use App\Charts\SmokeSensorChartLine;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(SmokeSensorChartLine $chart)
    {
        return view('admin.admin_index', ['chart' => $chart->build()]);
    }
}
