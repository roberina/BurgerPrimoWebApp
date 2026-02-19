<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|in:reservation,catering,feedback,other',
            'message' => 'required|string|max:5000',
        ]);

        try {
            Mail::send('emails.contact', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'subject' => $validated['subject'],
                'userMessage' => $validated['message'],
            ], function ($message) use ($validated) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to(config('mail.contact.address', 'info@burgerprimo.ee'))
                        ->subject('Uus kontaktvorm: ' . $this->getSubjectLabel($validated['subject']));
                $message->replyTo($validated['email'], $validated['name']);
            });

                   /*
        Mail::send('emails.contact-confirmation', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'userMessage' => $validated['message'],
        ], function ($message) use ($validated) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($validated['email'], $validated['name'])
                    ->subject('Täname teiega ühenduse võtmise eest - Burger Primo');
        });
        */

            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['Vabandust, sõnumi saatmisel tekkis viga. Palun proovige uuesti.']
            ]);
        }
    }

    private function getSubjectLabel($subject)
    {
        $labels = [
            'reservation' => 'Laua broneerimine',
            'catering' => 'Catering teenus',
            'feedback' => 'Tagasiside',
            'other' => 'Muu',
        ];

        return $labels[$subject] ?? 'Kontakt';
    }
}