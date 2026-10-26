<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer() 
                ? redirect()->route('shop') 
                : redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($input['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $input['login'],
            'password' => $input['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isCustomer()) {
                return redirect()->intended(route('shop'))
                    ->with('success', "Halo {$user->name}, selamat datang di Sweetie Dessert! Selamat memilih dessert lezat favoritmu. 🍰✨");
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}! 🍰");
        }

        return back()->withErrors([
            'login' => 'Kombinasi akun atau password salah.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('shop');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'phone'    => ['required', 'string', 'max:30'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'address'  => ['nullable', 'string', 'max:500'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'customer',
            ]);

            Customer::create([
                'user_id' => $user->id,
                'name'    => $validated['name'],
                'phone'   => $validated['phone'],
                'email'   => $validated['email'],
                'address' => $validated['address'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop')->with('success', 'Pendaftaran akun Customer berhasil! Selamat berbelanja dessert manis favoritmu. 🌸🧁');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout dari Sweetie Dessert.');
    }
}
