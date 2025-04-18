<?php

namespace App\Http\Controllers;

class InvestorController extends Controller
{
    public function index()
    {
        return view('dashboards.investor'); // pastikan view ini ada: resources/views/dashboards/investor.blade.php
    }
}