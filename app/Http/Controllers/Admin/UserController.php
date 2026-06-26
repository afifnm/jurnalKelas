<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles')->withTrashed();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($role = $request->role) {
            $query->role($role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = ['admin', 'guru', 'ks'];

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'nama'      => $request->nama,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => $request->password,
            'no_hp'     => $request->no_hp,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->assignRole($request->role);

        return response()->json(['message' => 'User berhasil ditambahkan.', 'user' => $user->load('roles')]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['nama', 'username', 'email', 'no_hp']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);
        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'User berhasil diperbarui.', 'user' => $user->fresh()->load('roles')]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    public function restore(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['message' => 'User berhasil dipulihkan.']);
    }
}
