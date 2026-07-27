<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:customer,user,manager,content editor,admin,super admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|in:customer,user,manager,content editor,admin,super admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($user->id === auth()->id() && $user->isSuperAdmin() && $data['role'] !== 'super admin') {
            return back()->withErrors(['role' => 'You cannot remove your own Super Admin access.']);
        }
        if ($user->isSuperAdmin() && $data['role'] !== 'super admin' && User::where('role', 'super admin')->count() <= 1) {
            return back()->withErrors(['role' => 'Cannot demote the final Super Admin.']);
        }

        // Prevent self-demotion of the only admin
        if ($user->role === 'admin' && $data['role'] !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['role' => 'Cannot demote the only Admin.']);
            }
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin() && User::where('role', 'super admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the final Super Admin.');
        }
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the only Admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];

        // Safety: filter out current user and last admin
        $safeIds = User::whereIn('id', $ids)
            ->where('id', '!=', auth()->id())
            ->get()
            ->filter(function ($user) {
                if ($user->role === 'super admin' && User::where('role', 'super admin')->count() <= 1) {
                    return false;
                }
                if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
                    return false;
                }

                return true;
            })
            ->pluck('id');

        User::whereIn('id', $safeIds)->delete();

        return redirect()->route('admin.users.index')->with('success', count($safeIds).' user(s) deleted.');
    }
}
