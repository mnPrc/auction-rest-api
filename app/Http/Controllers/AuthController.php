<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $token = Auth::login($user);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(Request $request){
        $login_credentials = $request->only(['email', 'password']);
        $token = Auth::attempt($login_credentials);

        if(!$token){
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => Auth::user(),
        ]);
    }

    public function getActiveUser(){
        $activeUser = Auth::user();
        return response()->json($activeUser);
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            'logout' => true
        ]);
    }
}
