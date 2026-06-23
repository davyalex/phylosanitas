<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index()
    {
        $user = User::with('roles')->withCount('posts')->orderBy('created_at', 'desc')->get();
        $role = Role::orderBy('name')->get();
        return view('admin.pages.Auth.register', compact('user', 'role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'phone'    => 'required|unique:users,phone',
            'email'    => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        Alert::toast('Utilisateur créé avec succès', 'success');
        return back();
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'role'  => 'nullable|exists:roles,name',
        ]);

        $data = [
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles($request->role);
        }

        Alert::toast('Utilisateur modifié avec succès', 'success');
        return back();
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            Alert::toast('Vous ne pouvez pas supprimer votre propre compte.', 'warning');
            return back();
        }

        $user->delete();
        Alert::toast('Utilisateur supprimé avec succès', 'success');
        return back();
    }

    public function lock($id)
    {
        User::findOrFail($id)->update(['active' => 'no']);
        Alert::toast('Accès bloqué', 'warning');
        return back();
    }

    public function unlock($id)
    {
        User::findOrFail($id)->update(['active' => 'yes']);
        Alert::toast('Accès restauré', 'success');
        return back();
    }

    public function loginForm()
    {
        return Auth::check()
            ? redirect('admin/')
            : view('admin.pages.Auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('phone', 'password'))) {
            $request->session()->regenerate();
            Alert::success('Connexion réussie');
            return redirect()->intended('admin/');
        }

        Alert::error('Contact ou mot de passe incorrect');
        return back()->withInput(['phone' => $request->phone]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Alert::success('Déconnexion réussie');
        return redirect('login');
    }

    public function profil($id)
    {
        $user = User::with('roles')->withCount('posts')->findOrFail($id);
        return view('admin.pages.Auth.profil', compact('user'));
    }

    public function newpassword(Request $request, $id)
    {
        $request->validate([
            'password'    => 'required',
            'newpassword' => 'required|min:6',
        ]);

        $user = User::findOrFail($id);

        if (!Hash::check($request->password, $user->password)) {
            Alert::error('Votre ancien mot de passe est incorrect.');
            return back();
        }

        $user->update(['password' => Hash::make($request->newpassword)]);
        Alert::success('Mot de passe modifié avec succès');
        return back();
    }
}
