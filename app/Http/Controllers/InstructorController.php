<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructorController extends Controller
{
    public function index()
    {
        return inertia('instructor/Dashboard');
    }
    public function settings()
    {
        return inertia('settings/Profile');
    }
}
