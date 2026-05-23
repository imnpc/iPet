<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginWithPassword(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()
            ->onlyInput('email')
            ->withErrors([
                'email' => '邮箱或密码不正确，请重试',
            ]);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function settings()
    {
        $user = Auth::user();
        $passkeys = $user->passkeys ?? collect();

        return view('auth.settings', compact('passkeys'));
    }

    public function passkeyOptions()
    {
        try {
            $user = Auth::user();
            $options = app(GeneratePasskeyRegisterOptionsAction::class)->execute($user);

            session()->put('passkey-registration-options', $options);

            return response()->json(json_decode($options, true));
        } catch (\Throwable $e) {
            \Log::error('Passkey options generation failed: '.$e->getMessage());

            return response()->json(['message' => '生成 Passkey 选项失败：'.$e->getMessage()], 500);
        }
    }

    public function storePasskey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'passkey' => 'required|string',
        ]);

        $user = Auth::user();
        $passkeyOptions = session()->pull('passkey-registration-options');

        app(StorePasskeyAction::class)->execute(
            $user,
            $request->input('passkey'),
            $passkeyOptions,
            $request->getHost(),
            ['name' => $request->input('name')]
        );

        return response()->json(['success' => true]);
    }

    public function deletePasskey(Request $request, $passkeyId)
    {
        $user = Auth::user();
        $user->passkeys()->where('id', $passkeyId)->delete();

        return back()->with('success', 'Passkey 已删除');
    }
}
