<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('users')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();
        Permission::create($validated);
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $validated = $request->validated();
        $permission->update($validated);
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
} 