<?php

// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;

// class LoginController extends Controller
// {
//     /*
//     |--------------------------------------------------------------------------
//     | Login Controller
//     |--------------------------------------------------------------------------
//     |
//     | This controller handles authenticating users for the application and
//     | redirecting them to your home screen. The controller uses a trait
//     | to conveniently provide its functionality to your applications.
//     |
//     */

//     use AuthenticatesUsers;

//     /**
//      * Where to redirect users after login.
//      *
//      * @var string
//      */
//     protected $redirectTo = '/home';

//     /**
//      * Create a new controller instance.
//      *
//      * @return void
//      */
//     public function __construct()
//     {
//         $this->middleware('guest')->except('logout');
//         $this->middleware('auth')->only('logout');
//     }
// }
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    //  Este método redirige a Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }



    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        if (!str_ends_with($googleUser->getEmail(), '@grupogranauto.mx')) {
            return redirect('/login')->withErrors([
                'email' => 'Solo se permiten correos @grupogranauto.mx.',
            ]);
        }

        // Buscar si ya existe
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Si existe, actualiza nombre e ID de Google (pero no contraseña)
            $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
            ]);
        } else {
            // Si NO existe, crea usuario y asigna una contraseña temporal (el usuario puede cambiarla después)
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt('12345678'), // Puedes usar un generador si quieres más seguridad
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/home');
    } catch (\Exception $e) {
        return redirect('/login')->withErrors(['google_error' => 'Hubo un error con Google']);
    }
}


//     //  Este método recibe la respuesta de Google
//     public function handleGoogleCallback()
// {
//     try {
//         $googleUser = Socialite::driver('google')->stateless()->user();

//         // Validar que el correo termine en @grupogranauto.mx
//         if (!str_ends_with($googleUser->getEmail(), '@grupogranauto.mx')) {
//             return redirect('/login')->withErrors([
//                 'email' => 'Solo se permiten correos @grupogranauto.mx.',
//             ]);
//         }

//         $user = User::updateOrCreate(
//             ['email' => $googleUser->getEmail()],
//             [
//                 'name' => $googleUser->getName(),
//                 'google_id' => $googleUser->getId(),
//                 'password' => bcrypt('dummy-password'),
//             ]
//         );

//         Auth::login($user);

//         return redirect()->intended('/home');
//     } catch (\Exception $e) {
//         return redirect('/login')->withErrors(['google_error' => 'Hubo un error con Google']);
//     }
// }




}
