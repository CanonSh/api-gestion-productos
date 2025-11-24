<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
    public function register(Request $r){
        try{
            $r->validate([
                'name'=>'required|string|max:255|',
                'email'=>'required|string|email|max:255|unique:users',
                'password'=> 'required|confirmed|min:8'
                ]);
            
            $user= User::create([
                'name'=>$r->name,
                'email'=>$r->email,
                'password'=>Hash::make($r->password)
            ]);

            return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
            ],201);
        }
        catch(ValidationException $valEx){
            return response()->json([
            'message' => 'Datos inválidos',
            'errors' => $valEx->errors()
            ],422);
        }
        catch(QueryException $qEx){
            return response()->json([
            'message' => 'Datos duplicados',
            'error' => $qEx->getMessage()
            ],409);
        }
        catch(Exception $ex){
            return response()->json([
            'message' => 'Error registrando al usuario',
            'error' => $ex->getMessage()
            ],500);
        }
    }

    public function login(Request $r){
        try{
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
                'token' => $token
            ],200);

            }
            else{
            return response()->json([
                'message'=>'Credenciales inválidas'
            ],401);}
        }
        catch(ValidationException $valEx){
            return response()->json([
            'message' => 'Datos inválidos',
            'errors' => $valEx->errors()
            ],422);
        }
        catch(Exception $ex){
            return response()->json([
            'message' => 'Error iniciando sesión',
            'error' => $ex->getMessage()
            ],500);
        }

    }

    public function logout(Request $request){
        try{
        $user = $request->user();

        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'User logged out successfully'
        ],200);
        }
        catch(Exception $ex){
            return response()->json([
            'message' => 'Error cerrando sesión',
            'error' => $ex->getMessage()
            ],500);
        }
    }

}
