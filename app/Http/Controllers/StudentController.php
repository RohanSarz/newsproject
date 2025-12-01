<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return inertia('student/Dashboard');
    }

    public function store(Request $request)
    {
        // Your store logic here
        return response()->json(['message' => 'Success']);
    }

    public function update(Request $request, $id)
    {
        // Your update logic here
        return response()->json(['message' => 'Updated']);
    }
}
