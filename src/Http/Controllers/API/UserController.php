<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Carbon\Carbon;
use Go2Flow\SaasRegisterLogin\Http\Requests\Api\UserCreateRequest;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\User;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

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
            $user = $this->userRepository->create($request->get('user'));
            $team = $this->teamRepository->create($request->get('team'), $user);
            $user->teams()->attach($team->id);

            $success = true;
            $message = 'User register successfully';

            event(new Registered($user));
        } catch (\Illuminate\Database\QueryException $ex) {
            $user = null;
            $success = false;
            $message = $ex->getMessage();
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
        $authResult = Auth::attempt($credentials);
        $needVerification = false;
        $success = false;
        if (!$user) {
            $message = 'Unauthorised';
        } elseif ($authResult && $user->hasVerifiedEmail()) {
            $success = true;
            $message = 'User login successfully';
            setSaasTeamId(auth()->user()->teams->first()->id);
        } elseif (!$user->hasVerifiedEmail() && $authResult) {
            $needVerification = $user->id;
            $message = 'Your E-Mail-Address is not verified!';
            auth()->logout();
        } else {
            $message = 'Unauthorised';
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

        Auth::login($user);
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
                'success' => true
            ], 202)
            : back()->with('resent', true);
    }

    public function impersonate(User $user, Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        dd($user);
    }

    private function localizeUrl(string $url) {
        if (config('saas-register-login.is_multi_language', false)) {
            $url = '/'.app()->getLocale().$url;
        }
        return $url;
    }
}
