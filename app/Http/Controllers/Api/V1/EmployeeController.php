<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // GET /api/v1/employees (Handles list, search, and department filter)
    public function index(Request $request)
    {
        $query = Employee::query();

        // GET /employees?search=juan
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // GET /employees?department=IT
        if ($request->has('department')) {
            $query->where('department', $request->query('department'));
        }

        return response()->json($query->get(), 200);
    }

    // POST /api/v1/employees
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:employees,email',
            'department' => 'required|string|max:255',
            'position'   => 'required|string|max:255',
        ]);

        $employee = Employee::create($validated);

        return response()->json($employee, 201);
    }

    // GET /api/v1/employees/{id}
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }

    // PUT /api/v1/employees/{id}
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:employees,email,' . $id,
            'department' => 'sometimes|string|max:255',
            'position'   => 'sometimes|string|max:255',
        ]);

        $employee->update($validated);

        return response()->json($employee, 200);
    }

    // DELETE /api/v1/employees/{id}
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }
}