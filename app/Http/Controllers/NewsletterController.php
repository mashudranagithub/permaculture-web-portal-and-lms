<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ], [
            'email.required' => __('Please provide an email address.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.unique' => __('You are already subscribed to our newsletter!'),
        ]);

        Subscriber::create([
            'email' => $request->email,
        ]);

        return redirect()->back()->with('message', __('Thank you for subscribing to our newsletter!'));
    }
}
