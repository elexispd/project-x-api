<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;
    public function register(RegisterUserRequest $request) {
        $request->validated($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);
        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('authToken'.$user->id)->plainTextToken,

        ]);


    }

    public function login(LoginUserRequest $request) {
        $request->validated($request->all());
        if(!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('', 'Invalid login details', 401);
        }

        $user = User::where('email', $request->email)->first() ;
        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('authToken'.$user->user)->plainTextToken,

        ]);
    }








}
