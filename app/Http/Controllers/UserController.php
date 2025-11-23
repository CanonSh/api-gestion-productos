<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    //
    public function register(Request $r){
        try{
            $r->validate([
                'name'=>'required|string|max:255|',
                'email'=>'required|string|email|max:255|unique:users',
                'password'=> 'required|confirmed|min8'
                ]);
            
            $user= User::create([
                'name'=>$r->name,
                'email'=>$r->email,
                'password'=>Hash::make($r->password)
            ]);

            return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'status' => 201
            ],201);
        }
        catch(Exception $ex){
            return response()->json([
            'message' => 'Error registrando al usuario',
            'error' => $ex->getMessage()
            ],500);
        }
    }

    public function login(Request $r){
            $r->validate([
                'email'=>'required|string|email',
                'password'=>'required|string'
            ]);

            $credentials = $r->only('email', 'password');

            if(Auth::attempt($credentials)){
                $user = Auth::user();

                $expiration = Carbon::now()->addMinutes(30);
                $token=$user->createToken('authentication_token',[], $expiration)->plainTextToken;
                
                return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => $user,
                'token' => $token,
                'status' => 200
            ],200);

            }
            else{
            return response()->json([
                'message'=>'Credenciales inválidas',
                'status'=>401
            ],401);
        }

    }

}
