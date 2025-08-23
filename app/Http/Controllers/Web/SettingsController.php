<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EnergyTariff;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Alert;

class SettingsController extends Controller
{
    // Mostrar perfil com dados e formulário de troca de senha
    public function profile()
    {
        $user = auth()->user();
        return view('settings.profile', compact('user'));
    }
    public function notifications()
    {
        $user = auth()->user();
        $alert = Alert::where('user_id', auth()->id())
            ->where('is_resolved', false)
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(10);
        return view('settings.notifications', [
            'user'  => $user,
            'alert' => $alert
        ]);
    }
    // Atualizar senha
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Senha atualizada com sucesso!');
    }
}
