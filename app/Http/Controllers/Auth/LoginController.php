<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        // Create a data array to hold all session variables we want to pass to the view
        $data = [];

        // Check for all possible session variables we might need
        $sessionVars = ['status', 'error', 'success', 'login', 'registration_complete'];

        foreach ($sessionVars as $var) {
            if ($request->session()->has($var)) {
                $data[$var] = $request->session()->get($var);
            }
        }

        // Pass all session data to the view
        return view('auth.login', $data);
    }

    /**
     * Create a new controller instance.
     *
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $input = $request->all();
        $this->validate($request, [
            'login' => 'required',
            'password' => 'required',
        ]);
        
        // Check if user exists
        $user = User::where('login', $input['login'])->first();
        if (!$user) {
            Log::notice('Failed login attempt: User not found: ' . $input['login']);
            return redirect()->route('login')
                ->with('error', 'Studentnummer of wachtwoord is onjuist.');
        }
        
        if (auth()->attempt(array('login' => $input['login'], 'password' => $input['password']))) {
            Log::info('Login successful for user: ' . $input['login']);
            
            if (auth()->user()->role == 'traveller') {
                return redirect()->route('traveller.home');
            } else if (auth()->user()->role == 'guide') {
                return redirect()->route('guide.home');
            } else if (auth()->user()->role == 'admin') {
                return redirect()->route('admin.home');
            } else if (auth()->user()->role == 'guest') {
                return redirect()->route('guest.disclaimer');
            } else {
                return redirect()->route('home');
            }
        } else {
            Log::notice('Failed login attempt: Invalid password for user: ' . $input['login']);
            return redirect()->route('login')
                ->with('error', 'Studentnummer of wachtwoord is onjuist.');
        }
    }
}
