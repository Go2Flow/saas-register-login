<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Requests\Api\UserCreateRequest;
use Go2Flow\SaasRegisterLogin\Models\User;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\UserRepositoryInterface;
use http\Client\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class ApiController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        /** @var User $user */
        $user = User::whereEmail($request->email)->first();

        $authResult = auth('web')->attempt($credentials);

        $needVerification = false;
        $success = false;
        $userToken = null;
        $team = null;

        if (!$user) {
            $message = 'Wrong password or e-mail-address!';
        } elseif ($authResult && $user->hasVerifiedEmail()) {
            $success = true;
            $message = 'User login successfully';

            if($user->tokens()->count()){
                $userToken = $user->tokens()->first()->plain_text_token;
            }else{
                $accessToken = $user->createToken($this->makeTokenName($user));

                $token = PersonalAccessToken::findToken($accessToken->plainTextToken);
                $token->plain_text_token = $accessToken->plainTextToken;
                $token->save();
                $userToken = $token->plain_text_token;
            }

            $team = auth('web')->user()->teams->first();
            if ($team) {
                setSaasTeamId($team->id);
            }
        } elseif (!$user->hasVerifiedEmail() && $authResult) {
            $needVerification = $user->id;
            $message = 'Your E-Mail-Address is not verified!';
            auth('web')->logout();
        } else {
            $message = 'Wrong password or e-mail-address!';
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
            'team' => $team,
            'user' => $user,
            'token' => $userToken,
            'need_verification' => $needVerification
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

    private function makeTokenName(User $user): string
    {
        $name = strtolower($user->lastname).'_'.strtolower($user->firstname);
        return $name.'_ApiToken';
    }
}