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
        if (!$user) {
            $message = 'Wrong password or e-mail-address!';
        } elseif ($authResult && $user->hasVerifiedEmail()) {
            $success = true;
            $message = 'User login successfully';
            $token = $user->createToken($this->makeTokenName($user))->plainTextToken;
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
            'token' => $token,
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

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resend(User $user, Request $request)
    {
        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect($this->localizeUrl('/login'));
        }

        $user->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? new JsonResponse([
                'success' => true,
            ], 202)
            : back()->with('resent', true);
    }

    public function impersonate(User $user, Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        auth('web')->login($user);
        setSaasTeamId($user->teams->first()->id);
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($this->localizeUrl('/login'));
    }

    public function sendResetPasswordMail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $response = Password::broker()->sendResetLink($request->only('email'));
        $success = false;
        if ($response === Password::broker()::RESET_LINK_SENT) {
            $success = true;
        }
        return $request->wantsJson()
            ? new JsonResponse([
                'success' => $success,
                'message' => $response
            ], 202)
            : back()->with('send_password_reset', true);
    }

    public function passwortReset(Request $request)
    {
        $token = $request->route()->parameter('token');
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($this->localizeUrl('/reset/password/'.$token.'/'.$request->email));
    }

    public function passwordResetSave(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        $success = false;
        if ($status === Password::PASSWORD_RESET) {
            $success = true;
            $user = User::whereEmail($request->email)->first();
            if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
                event(new Verified($user));
            }
            auth('web')->login($user);
            setSaasTeamId($user->teams->first()->id);
        }
        return new JsonResponse([
            'success' => $success,
            'message' => $status
        ], 202);
    }

    private function makeTokenName(User $user): string
    {
        $name = strtolower($user->lastname).' '.strtolower($user->firstname);
        $name = ucwords($name);
        return str_replace(' ', '', $name);
    }
}