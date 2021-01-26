<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidateUserLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\MyResponseBuilder as MRB;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', new ValidateUserLoginRequest($request)],
            'password' => ['required']
        ];

        $request->validate($rules);

        $user = User::where('email', $request->email)->first();

        return MRB::asSuccess()
            ->withData([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'access_token' => $user->createToken($request->email)->plainTextToken,
                'msg' => 'Login Success !!!'
            ])
            ->withHttpCode(200)
            ->build();
    }

    public function signUp(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed']
        ];

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return MRB::asSuccess()
            ->withData([
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'AUTH_TOKEN' => $user->createToken($request->email)->plainTextToken
            ])
            ->withHttpCode(200)
            ->build();
    }

    public function logout()
    {
        Auth::logout();
    }

}
