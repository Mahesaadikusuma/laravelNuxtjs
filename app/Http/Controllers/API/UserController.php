<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Helpers\ResponseFormatter;


class UserController extends Controller
{

    public function login(Request $request)
    {

        try {
            // TODO: VALIDATION REQUEST
            $request->validate([
                "email" => 'required|email',
                "password" => 'required'
            ]);
        
        // TODO: FIND UNSER BY EMAIL
        
        $credential = request(['email', 'password']);
        if (!Auth::attempt($credential)) {
            return ResponseFormatter::error("Unauthorized", 401);
        }

        $user = User::where("email", $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            throw new Exception("Invalid Password");
            
        }

        // TODO: GENERATE TOKEN
        $tokenResult = $user->createToken("authToken")->plainTextToken;
        
        // TODO: RETURN RESPONSE
        return ResponseFormatter::success([
            
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user,

        ], "Login success");
        } catch (Exception $e) {
            return ResponseFormatter::error("Authentication Failde", 400);
        }
    }

    public function register(Request $request)
    {
        try {
            // TODO: validate user
            $request->validate([
                "name" => ["required", "string", "max:255"],
                "email" => ["required", "string", "email" ,"max:255", "unique:users"],
                'password' => ['required', 'string', new Password],
            ]);

            // TODO: create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

             // Generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            // TODO: return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register success');

        } catch (Exception $error) {
             // TODO: Return error response
            return ResponseFormatter::error($error->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // TODO: REMOVE TOKEN
        $token = $request->user()->currentAccessToken()->delete();

        // RETURN TOKEN
        return ResponseFormatter::success($token, 'logout success');

    }

    public function fetch(Request $request)
    {
        // GET DATA USER
        $user = $request->user();
        // bisa pakai cara ini $request->user()
        // bisa pakai cara ini Auth::user()


        // return reponse
        return ResponseFormatter::success($user, 'Fetch Success');
    }
}
