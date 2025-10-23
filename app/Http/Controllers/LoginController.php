<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $tokenName = $request->header('user-agent');
        if (filled($request->email) && filled($request->password)) {
            
            $validateData = $request->validate([
                'email'    => ['required', 'max:255',],
                'password' => ['required', 'max:255'],
            ]);

            $user = User::query()->firstWhere('email', $request->email);

            if (blank($user) || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages(['pass' => 'Неверный логин или пароль.']);
            }

            //старые токены удалим
            $query = $user->tokens();
            $query->where('name', $tokenName);
            $query->delete();

            $token = $user->createToken($tokenName)->plainTextToken;
            
            $user->remember_token = Hash::make($token);
            $user->save();
        }
        else
        {
            return response()->json(['message' => 'пользователь уже существует'], 200);
        }

        return response()->json(['message' => 'пользователь успешно создан', 'token' => $token], 200);
    }
}
