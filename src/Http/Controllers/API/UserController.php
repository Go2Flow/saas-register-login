<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Register
     */
    public function register(Request $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $success = true;
            $message = 'User register successfully';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    /**
     * Login
     * @TODO: session team_id setzen bei Login
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];


        if (Auth::attempt($credentials)) {
            $success = true;
            $message = 'User login successfully';
            setSaasTeamId(\auth()->user()->teams->first()->id);
        } else {
            $success = false;
            $message = 'Unauthorised';
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    /**
     * Logout
     */
    public function logout()
    {

        try {
            // Get user who requested the logout
            $user = request()->user(); //or Auth::user()
            // Revoke current user token
            $user->tokens()->delete();

            session()->flush();
            session()->regenerate();

            $success = true;
            $message = 'Successfully logged out';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }
}
