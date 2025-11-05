<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'type' => 'required|in:general,support,sales,b2b,complaint',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Aqui você pode configurar o envio de email
            // Por enquanto, vamos apenas simular o sucesso
            $contactData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => $request->type,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ];

            // TODO: Implementar envio de email
            // Mail::to('contato@feiradasfabricas.com')->send(new ContactMail($contactData));

            return redirect()->route('contact')
                ->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar mensagem. Tente novamente mais tarde.')
                ->withInput();
        }
    }
}
