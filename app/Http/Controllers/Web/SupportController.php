<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\SupportMessage;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|min:10',
        ]);

        // Enviar o e-mail
        Mail::to('wattalyze@gmail.com')->send(new SupportMessage($data));

        return back()->with('success', 'Sua mensagem foi enviada com sucesso!');
    }
}
