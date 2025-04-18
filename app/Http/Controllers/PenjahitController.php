<?php

namespace App\Http\Controllers;

class PenjahitController extends Controller
{
    public function index()
    {
        return view('dashboards.penjahit'); // pastikan view ini ada: resources/views/dashboards/penjahit.blade.php
    }
}