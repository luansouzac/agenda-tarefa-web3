<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'name.required' => 'Informe o nome',
            'email.required' => 'Informe o e-mail',
            'email.email' => 'Informe um e-mail válido',
            'email.unique' => 'Este e-mail já está cadastrado',
            'password.required' => 'Informe a senha',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas não conferem'
        ]);

        $user = User::create($validate);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user'=>$user,
            'token'=>$token], 201);
    }

    public function login(Request $request){
        $validate = $request->validate([
            'email'=> 'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $validate['email'])->
            first();

        if (!$user || !Hash::check($validate['password'], 
                    $user->password)){
            return response()->json(['erro'=>
                'Usuário ou senha incorreto'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['user'=>$user, 
            'token'=>$token], 200);
    }

    public function me(Request $request){
        return response()->json($request->user(), 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensagem'=>'Logout realizado'], 200);
    }
}
