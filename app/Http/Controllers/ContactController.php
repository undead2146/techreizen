<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mail;
use App\Models\Trip;

class ContactController extends Controller
{
    public function showContactForm(): View
    {
        $trips = Trip::orderBy('name')->get();

        return view('contact.form')
            ->with('trips', $trips);
    }

    public function submitContactForm(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100'],
            'trip' => ['required'],
            'message' => ['required', 'string', 'max:1000'],
            'cf-turnstile-response' => ['required'],
        ], [
            'cf-turnstile-response.required' => 'CAPTCHA challenge failed. Please try again.',
        ]);

        $tripId = $request->input('trip');
        $trip = Trip::find($tripId);

        if (!$trip) return redirect()
            ->back()
            ->withInput()
            ->withErrors(['trip' => 'The selected trip does not exist.']);

        $tripName = $trip->name;
        $tripContactEmail = $trip->contact_email;
        $userEmail = $request->input('email');
        $userFullName = $request->input('first_name') . ' ' . $request->input('last_name');

        //REMARK: maybe send email once to trip adviser and put user in CC or send email once with multiple recipients
        try {
            // We make seperate ContactMail instance for each email because if we use the same object it gives some bugs with the queueing of the emails
            $contactMailForTripContact = new ContactMail($tripName, $userFullName, $userEmail, $request->input('message')); // make CantactMail instance for email to trip adviser
            Mail::to($tripContactEmail)->queue($contactMailForTripContact); // send email with the user message to trip adviser

            $contactMailForUser = new ContactMail($tripName, $userFullName, $userEmail, $request->input('message')); // make CantactMail instance for email to user
            Mail::to($userEmail)->queue($contactMailForUser); // send confirmation email to user
        } catch (\Exception $ex) {
            \Log::error("Failed to dispatch contact mails: " . $ex->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['mail' => 'There was an error sending your message. Please try again later.']);
        }

        return redirect()
            ->route('contact.confirmation', ['first_name' => $request->input('first_name')]);
    }

    public function showContactConfirmation(Request $request) : View
    {
        $firstName = $request->query('first_name');

        return view('contact.confirmation')
            ->with('first_name', $firstName);
    }
}
