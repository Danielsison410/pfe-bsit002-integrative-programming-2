<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
public function index(Request $request)
{
$query = Employee::with('department');

if ($request->has('search')) {
$search = $request->search;

$query->where(function ($q) use ($search) {
$q->where('first_name', 'like', "%{$search}%")
->orWhere('last_name', 'like', "%{$search}%")
->orWhere('email', 'like', "%{$search}%");
});
}

if ($request->has('department_id')) {
$query->where('department_id', $request->department_id);
}

return response()->json($query->paginate(10));
}

public function store(Request $request)
{
$validated = $request->validate([
'first_name'    => 'required|string|max:255',
'last_name'     => 'required|string|max:255',
'email'         => 'required|email|unique:employees,email',
'department_id' => 'required|exists:departments,id',
'position'      => 'required|string|max:255',
]);

$employee = Employee::create($validated);

return response()->json($employee, 201);
}

public function show($id)
{
$employee = Employee::find($id);

if (!$employee) {
return response()->json(['message' => 'Employee not found'], 404);
}

return response()->json($employee);
}

public function update(Request $request, $id)
{
$employee = Employee::find($id);

if (!$employee) {
return response()->json(['message' => 'Employee not found'], 404);
}

$validated = $request->validate([
'first_name' => 'sometimes|string|max:100',
'last_name' => 'sometimes|string|max:100',
'email' => 'sometimes|email|unique:employees,email,' . $id,
'department' => 'sometimes|string|max:100',
'position' => 'sometimes|string|max:100',
]);
}
}