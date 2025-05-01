<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->search) {
            $query->where('Nama_Role', 'LIKE', '%' . $request->search . '%');
        }

        $roles = $query->get();

        return view('admin.role.index', compact('roles'));
    }

    // public function create()
    // {
    //     return view('admin.role.create');
    // }

    public function store(Request $request)
    {
        try {
            // $request->validate([
            //     'Nama_Role' => 'required',
            // ]);

            $role = new Role();
            $role->Nama_Role = $request->Nama_Role;
            $role->save();

            return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('role.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $editData=Role::findOrFail($id);
        return view('admin.role.edit', compact('editData'));
    }


    public function update(Request $request, $id)
    {
        $role=Role::findOrFail($id);
        $role->Nama_Role = $request->Nama_Role;
        $role->save();

        return redirect()->route('role.index')->with('success', 'Role berhasil diperbarui');
    }

    public function delete(Role $id): RedirectResponse
    {

        $id->delete();
        return redirect()->route('role.index')->with('success', 'Role berhasil dihapus');
        
    }
}
