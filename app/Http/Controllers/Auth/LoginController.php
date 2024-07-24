<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Log in account user.
     *
     * @return View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Log out account user.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();

                $profileModules = $user->profile->userProfileModules;

                $modulesCanAccess = [];
                foreach ($profileModules as $module) {
                    array_push($modulesCanAccess, [
                        "module" => $module->module,
                        "permissions" => $module->permissions
                    ]);
                }

                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'result' => 'success',
                    'success' => true,
                    'user' => [
                        'id'    => $user->id,
                        'token' => $token,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'profile' => auth()->user()->profile->role,
                        'canAccess' => $modulesCanAccess
                    ]
                ]);
            }
            return response()->json(['result' => 'failed', 'success' => false]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => 'failed',
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function redirectProvider(Request $request)
    {
        return Socialite::driver($request->platform)->redirect();
    }

    /**
     * Handle an authentication with provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function providers(Request $request)
    {
        $extUser = Socialite::driver($request->platform)->user();
        $user    = User::where('email', $extUser->email)->first();
        $data    = [
            'name'      => $extUser->name,
            'email'     => $extUser->email,
            'avatar'    => $extUser->avatar,
            'token'     => $extUser->token,
            'refresh_token' => $extUser->refreshToken,
            'phone'      => 0,
            'profile_id' => 1,
        ];

        if ($user) {
            $user->update([
                'token' => $data['token'],
                'refresh_token' => $data['refresh_token'],
            ]);
        } else {
            $user = User::create($data);
        }
        Auth::login($user);

        return redirect('/');
    }
}
