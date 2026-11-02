<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $existing = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existing) {
            if ($existing->status === 'unsubscribed') {
                $existing->update(['status' => 'active']);
                return back()->with('newsletter_success', 'Vous êtes de nouveau abonné à notre newsletter !');
            }
            return back()->with('newsletter_info', 'Vous êtes déjà abonné à notre newsletter.');
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
            'name'  => $request->input('name'),
        ]);

        return back()->with('newsletter_success', 'Merci ! Vous êtes maintenant abonné à notre newsletter.');
    }
}
