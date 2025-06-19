<?php
// Path: app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboards.admin'); // pastikan view ini ada: resources/views/dashboards/admin.blade.php
    }
}