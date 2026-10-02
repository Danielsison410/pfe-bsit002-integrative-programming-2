<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return response()->json(Department::all(), 200);
    }

    // POST /api/v1/departments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:departments,name|max:255',
        ]);

        $department = Department::create($validated);

        return response()->json($department, 201);
    }

    // GET /api/v1/departments/{id}
    public function show($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        return response()->json($department, 200);
    }
}
