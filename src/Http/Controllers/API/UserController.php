<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Requests\Api\UserCreateRequest;
use Go2Flow\SaasRegisterLogin\Models\User;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserController extends Controller
{

    private UserRepositoryInterface $userRepository;
    private TeamRepositoryInterface $teamRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        TeamRepositoryInterface $teamRepository
    ) {
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param UserCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserCreateRequest $request)
    {
        try {

            $user = DB::transaction(function () use ($request) {
                $user = $this->userRepository->create($request->get('user'));
                $this->teamRepository->create($request->get('team'), $user);

                event(new Registered($user));

                return $user;
            });

            $success = true;
            $message = 'User register successfully';

        } catch (\Illuminate\Database\QueryException $ex) {
            $user = null;
            $success = false;
            $message = 'A technichal problem occoured, your team is informed. Please try again in 30min.';

            Log::error($ex->getMessage(), [
                'File' => __FILE__,
                'Line' => $ex->getLine(),
                'SQL' => $ex->getSql(),
                'Bindings' => $ex->getBindings()
            ]);
        } catch (\Exception $ex) {
            $user = null;
            $success = false;
            $message = 'A technichal problem occoured, your team is informed. Please try again in 30min.';

            Log::error($ex->getMessage(), [
                'File' => __FILE__,
                'Line' => $ex->getLine(),
                'Trace' => $ex->getTrace()
            ]);
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
            'user_id' => optional($user)->id
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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws AuthorizationException
     */
    public function verify(int $id, Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect($this->localizeUrl('/login'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        auth('web')->login($user);
        setSaasTeamId($user->teams->first()->id);

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect($this->localizeUrl('/backend'))->with('verified', true);
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
}
