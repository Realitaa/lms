<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return inertia('students/Index');
    }

    public function discover()
    {
        return inertia('students/Discover');
    }
}
