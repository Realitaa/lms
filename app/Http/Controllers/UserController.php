<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Enums\Roles;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at'])
            ->orderBy('name')
            ->get();

        $roles = array_map(fn(Roles $role) => $role->value, Roles::cases());

        return Inertia::render('Users', [
            'users' => $users,
            'roles' => $roles,
            'authUserId' => request()->user()?->id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in(array_map(fn(Roles $role) => $role->value, Roles::cases()))
            ],
        ])->validate();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->input('role') !== $user->role && $request->user()?->id === $user->id) {
            return back()->withErrors(['role' => 'Tidak dapat mengubah role sendiri.']);
        }

        $validated = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in(array_map(fn(Roles $role) => $role->value, Roles::cases()))
            ],
        ])->validate();

        if (array_key_exists('password', $validated) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if (request()->user()?->id === $user->id) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus diri sendiri.']);
        }

        $user->delete();

        return redirect()->route('users.index');
    }
}
