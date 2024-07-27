<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        
        $users = User::all();
        return view('users.index', compact('users'));
    
    }

    public function create() {
       
        return view('users.create');
    
    }

    public function store(Request $request) {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'level' => 'required|in:user,admin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário adicionado com sucesso.');
    }

    public function edit(User $user) {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user) {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'level' => 'required|in:user,admin',
        ]);

        $user->update($request->all());

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user) {
        
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}