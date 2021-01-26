<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\MyResponseBuilder as MRB;

class AuthController extends Controller
{
    public function signIn(Request $request): string
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return MRB::asError(422)
                ->withData(['error' => $validator->messages()->first()])
                ->build();
        }

        $user = User::where('email', $request->email)->first();

        return MRB::asSuccess()
            ->withData(['token' => $user->createToken($request->email)->plainTextToken])
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

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return MRB::asError(422)
                ->withData(['error' => $validator->messages()->first()])
                ->build();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return MRB::asSuccess()
            ->withData(['token' => $user->createToken($request->email)->plainTextToken])
            ->withHttpCode(200)
            ->build();
    }

    public function logout()
    {
        Auth::logout();
    }

}
