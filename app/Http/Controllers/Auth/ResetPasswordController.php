<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Traveller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/traveller/home';

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Here we will check if the token and email match in the password_reset_tokens table
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            Log::notice('Password reset attempt with invalid token or email: ' . $request->email);
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        if ($request->token !== $resetRecord->token) {
            Log::notice('Password reset attempt with mismatched token for: ' . $request->email);
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        // Find the traveller with this email
        $traveller = Traveller::where('email', $request->email)->first();
        if (!$traveller) {
            return $this->sendResetFailedResponse($request, 'We kunnen geen gebruiker vinden met dit e-mailadres.');
        }

        // Get the associated user
        $user = User::find($traveller->user_id);
        if (!$user) {
            return $this->sendResetFailedResponse($request, 'We kunnen geen gebruiker vinden voor dit account.');
        }

        try {
            // Reset the password
            $user->password = Hash::make($request->password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            
            // Delete the token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // Log the user in
            auth()->login($user);
            
            // Trigger the PasswordReset event
            event(new PasswordReset($user));
            
            Log::info('Password reset successful for user: ' . $user->login);
            return $this->sendResetResponse($request, Password::PASSWORD_RESET);
            
        } catch (\Exception $e) {
            Log::error('Exception during password reset: ' . $e->getMessage());
            return $this->sendResetFailedResponse($request, 'Er is een fout opgetreden bij het herstellen van uw wachtwoord.');
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('status', 'Uw wachtwoord is succesvol gewijzigd!');
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
