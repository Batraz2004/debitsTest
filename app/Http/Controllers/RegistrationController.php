<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function createUser(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]) && filled($request)) {
            $validateData = $request->validate([
                'email'    => ['required', 'max:255', 'unique:users'],
                'password' => ['required', 'max:255'],
                'name'     => ['required', 'max:255'],
            ]);
            
            $user = User::create($request->all());
            if (!$user)
                throw new Exception('не удалось зарегистрировать пользователя');
            
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->remember_token = Hash::make($token);
            $user->save();
        } else {
            return response()->json(['message' => 'пользователь уже существует'], 200);
        }

        return response()->json(['message' => 'пользователь успешно создан', 'token' => $token], 200);
    }
}
