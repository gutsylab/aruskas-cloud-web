<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        $tenant = request()->attributes->get('tenant');
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('auth.register', compact('tenant'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $tenant = request()->attributes->get('tenant');
        
        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($request->expectsJson()) {
            $token = $user->createToken('auth-token')->plainTextToken;
            
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'tenant' => $tenant,
                'token' => $token,
            ], 201);
        }

        return redirect('/dashboard')->with('success', 'Registration successful!');
    }
}
