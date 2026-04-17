<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:candidat,recruteur',
        ]);
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
        ]);
        return response()->json([
            'message'=>'User registered successfully',
            'user'=>$user,
        ],201);     
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|string'
        ]);
        $credentials=$request->only('email','password');// on recupere les champs email et password
        $token=Auth::guard('api')->attempt($credentials);// attampt va verifier les credentials et generer un token
        if(!$token){
            return response()->json([
                'message'=>'Invalid email or password'
            ],401);
        }
        return response()->json([
            'message'=>'login successfuly',
            'User'=>Auth::guard('api')->user(),
            'token'=>$token,
            'type_token'=> 'Bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ],200);
    }

    public function logout(){
        Auth::guard('api')->logout();
        return response()->json([
            'message'=>'logout successfuly'
        ],200);
    }

    public function me(){
        return response()->json([
            'user'=>Auth::guard('api')->user()
        ],200);
    }
}
