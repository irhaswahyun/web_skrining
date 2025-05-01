<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('nama', 'LIKE', '%' . $request->search . '%');
        }

        $users = $query->get();
        $roles = \App\Models\Role::all(); // Ambil semua data role dari tabel roles
        return view('admin.manajemen_pengguna.index', compact('users', 'roles')); // Kirimkan $users dan $roles ke view
    }

    // public function create()
    // {
    //     $roles = Role::all();
    //     return view('admin.manajemen_pengguna.create', compact('roles')); // Sesuaikan nama view
    // }

    public function store(Request $request)
    {
        try {
            //  $request->validate([
            //      'nama' => 'required|string|max:255',
            //      'email' => 'required|string|email|max:255|unique:users',
            //      'password' => 'required|string|min:8',
            //  ]);

            $user = new User();
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->password = Hash::make($request->password); // Hash password
            $user->id_role = $request->id_role; //tambahan
            $user->save();

            return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan'); // Sesuaikan route name
        } catch (\Throwable $th) {
            return redirect()->route('pengguna.index')->with('error', $th->getMessage()); // Sesuaikan route name
        }
    }

    public function edit($id)
    {
        $editData = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.manajemen_pengguna.edit', compact('editData','roles')); // Sesuaikan nama view
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->id_role = $request->id_role; //tambahan
        if ($request->password) { //kondisi jika password diubah
            $user->password = Hash::make($request->password); // Hash password
        }
        $user->save();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui'); // Sesuaikan route name
    }

    public function delete(User $id): RedirectResponse
    {

        $id->delete();
        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus'); // Sesuaikan route namer.index')->with('success', 'Pengguna berhasil dihapus'); // Sesuaikan route name

    }
}