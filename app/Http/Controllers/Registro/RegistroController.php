<?php

namespace App\Http\Controllers\Registro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    


    public function login(LoginRequest $request){
        $user = User::where('email',$request['email'])->first();
            if( !$user || !Hash::check($request['password'],$user->password)){

                return response()->json([
                    'res' => false,
                    'mensaje' => "Datos incorrectos",
                    'email' => $user->email
                ]);
            }
            $token = $user->createToken($request['email'])->plainTextToken;
            
            return response()->json([
                'res' =>true,
                'token' => $token
            ],201);
    }
}
