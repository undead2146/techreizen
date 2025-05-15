<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Traveller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Validate the student number for the given request.
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'login' => 'required|regex:/^[rub]\d{7}$/i',
        ], [
            'login.required' => 'Het studentnummer is verplicht.',
            'login.regex' => 'Het studentnummer moet beginnen met r, u of b en gevolgd worden door 7 cijfers.',
        ]);
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        try {
            // Find the user by their student number/login 
            $user = User::where('login', $request->login)->first();

            if (!$user) {
                Log::notice('Password reset request for non-existent user: ' . $request->login);
                return back()->withErrors([
                    'login' => ['We kunnen geen gebruiker vinden met dit studentnummer.'],
                ]);
            }

            // Find the traveller record for this user to get their email
            $traveller = Traveller::where('user_id', $user->id)->first();
            
            if (!$traveller || empty($traveller->email)) {
                Log::notice('Password reset request for user without email: ' . $user->id);
                return back()->withErrors([
                    'login' => ['We kunnen geen e-mailadres vinden dat gekoppeld is aan dit studentnummer.'],
                ]);
            }
            
            $email = $traveller->email;
            
            // Generate a token manually
            $token = Str::random(64);
            
            // Store the token in the password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['email' => $email, 'token' => $token, 'created_at' => now()]
            );
            
            // Create the reset URL
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $email,
            ], false));
            
            // Send custom reset email with URL
            \Mail::to($email)->send(new \App\Mail\PasswordResetMail($user, null, $resetUrl));
            
            // Create masked email for display
            $maskedEmail = $this->maskEmail($email);
            
            Log::info('Password reset link sent to user: ' . $user->login);
            return redirect()->route('login')
                ->with('status', "We hebben een link om uw wachtwoord te herstellen verstuurd naar {$maskedEmail}. Controleer uw e-mail.");

        } catch (\Exception $e) {
            Log::error('Exception in password reset: ' . $e->getMessage());
            
            return back()->withErrors([
                'login' => ['Er is een fout opgetreden bij het herstellen van uw wachtwoord. Probeer het later opnieuw.'],
            ]);
        }
    }

    /**
     * Mask an email address for privacy in messages.
     * 
     * @param string $email The email to mask
     * @return string The masked email
     */
    private function maskEmail($email)
    {
        if (empty($email)) {
            return '[onbekend e-mailadres]';
        }

        $emailParts = explode('@', $email);
        if (count($emailParts) !== 2) {
            return '[ongeldig e-mailadres]';
        }

        $name = $emailParts[0];
        $domain = $emailParts[1];

        // Show first 2 characters and last character of the name part
        $nameLength = strlen($name);
        if ($nameLength <= 3) {
            $maskedName = $name[0] . str_repeat('*', $nameLength - 1);
        } else {
            $maskedName = $name[0] . $name[1] . str_repeat('*', $nameLength - 3) . $name[$nameLength - 1];
        }

        return $maskedName . '@' . $domain;
    }
}
