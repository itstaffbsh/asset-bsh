<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionsByFeature = Permission::all()->groupBy('feature');
        $departments = \App\Models\Department::orderBy('nama_departemen')->get();
        return view('roles.create', compact('permissionsByFeature', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
            'dept_approval_for' => 'nullable|exists:departments,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'dept_approval_for' => $request->dept_approval_for,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', __('Role berhasil dibuat.'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|string'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('roles.index')->with('success', __('Role berhasil diperbarui.'));
    }

    public function detail(Role $role)
    {
        $permissionsByFeature = Permission::all()->groupBy('feature');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $departments = \App\Models\Department::orderBy('nama_departemen')->get();
        
        return view('roles.detail', compact('role', 'permissionsByFeature', 'rolePermissions', 'departments'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'dept_approval_for' => 'nullable|exists:departments,id'
        ]);

        $role->update([
            'dept_approval_for' => $request->dept_approval_for,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', __('Hak Akses Role berhasil diperbarui.'));
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'superadmin' || $role->slug === 'managing_director') {
            return redirect()->route('roles.index')->with('error', __('Role System (Super Admin / Managing Director) tidak dapat dihapus.'));
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', __('Role berhasil dihapus.'));
    }
}
