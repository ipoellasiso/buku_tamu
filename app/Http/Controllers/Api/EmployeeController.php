<?php

// app/Http/Controllers/Api/EmployeeController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // List pegawai (untuk dropdown pilih yang dituju)
    public function index()
    {
        $employees = User::whereIn('role', ['admin', 'employee'])
            ->select('id', 'name', 'position', 'department')
            ->orderBy('name')
            ->get();
        return response()->json($employees);
    }
}