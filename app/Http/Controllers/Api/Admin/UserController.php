<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Owner bisa lihat semua, admin hanya bisa lihat karyawan
        $user = auth()->user();
        if ($user->role === 'owner') {
             $users = User::where('id', '!=', $user->id)->get();
        } else {
             $users = User::where('role', 'karyawan')->get();
        }
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['admin', 'karyawan'])], // Owner tidak bisa dibuat dari sini
            'base_salary' => 'required|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'base_salary' => $request->base_salary,
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes','required','string','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8',
            'role' => ['sometimes','required', Rule::in(['admin', 'karyawan'])],
            'base_salary' => 'sometimes|required|numeric|min:0',
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    public function destroy(User $user)
    {
        // Mencegah menghapus role yang lebih tinggi atau sama
        if (auth()->user()->role !== 'owner' && $user->role === 'admin') {
             return response()->json(['message' => 'Admins cannot delete other admins.'], 403);
        }

        $user->delete(); // Soft delete
        return response()->json(['message' => 'User deleted successfully']);
    }
}
