<?php

namespace App\Http\Controllers;

class CeoController extends Controller
{
    public function index()
    {
        return view('dashboards.ceo'); // pastikan view ini ada: resources/views/dashboards/ceo.blade.php
    }
}