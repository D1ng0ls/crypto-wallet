<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'investment_profile' => 'required|in:aggressive,moderate,conservative',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'investment_profile.required' => 'O perfil de investimento é obrigatório.',
            'investment_profile.in' => 'O perfil de investimento deve ser agressivo, moderado ou conservador.',
        ]);

        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'investment_profile' => $request->investment_profile,
        ]);
        
        return redirect()->back()->with('success', 'Informações atualizadas com sucesso!');
    }

    public function editPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual não corresponde.',
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Senha atualizada com sucesso!');
    }
}
