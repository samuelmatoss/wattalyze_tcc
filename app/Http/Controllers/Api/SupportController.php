<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\SupportMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
class SupportController extends Controller
{
    /**
     * Submit a support request
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Add user information to the support message
        $user = auth()->user();
        $data['user'] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ];

        // Enviar o e-mail
        try {
            Mail::to('wattalyze@gmail.com')->send(new SupportMessage($data));
            
            return response()->json([
                'message' => 'Sua mensagem foi enviada com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get support contact information
     */
    public function contactInfo(): JsonResponse
    {
        return response()->json([
            'email' => 'wattalyze@gmail.com',
            'response_time' => '24-48 horas',
            'working_hours' => 'Segunda a Sexta, 9h às 18h'
        ]);
    }
}
