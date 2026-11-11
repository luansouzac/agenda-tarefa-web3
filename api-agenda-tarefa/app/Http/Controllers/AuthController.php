<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
}
