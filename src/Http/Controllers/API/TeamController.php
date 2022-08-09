<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Events\RemoveUserFromTeam;
use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Http\Requests\Api\TeamCreateRequest;
use Go2Flow\SaasRegisterLogin\Http\Requests\Api\TeamUpdateBankRequest;
use Go2Flow\SaasRegisterLogin\Http\Requests\Api\TeamUpdateGeneralRequest;
use Go2Flow\SaasRegisterLogin\Http\Requests\Api\UserInviteAcceptRequest;
use Go2Flow\SaasRegisterLogin\Http\Resources\Authenticated\UserResource;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Models\User;
use Go2Flow\SaasRegisterLogin\Repositories\PermissionRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TeamController extends Controller
{
    private PermissionRepositoryInterface $permissionRepository;
    private TeamRepositoryInterface $teamRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        PermissionRepositoryInterface $permissionRepository,
        TeamRepositoryInterface $teamRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
    }

    public function users()
    {
        $team = currentTeam();
        if ($team) {
            return UserResource::collection($team->users->load('roles'));
        }
        abort('401', 'Unauthenticated');
    }

    /**
     * @return array
     */
    public function roles()
    {
        return $this->permissionRepository->getRoles();
    }

    public function invite(Team $team, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role_id' => 'required',
        ]);
        $success = $this->teamRepository->invite($team, $request->get('email'), $request->get('role_id'));
        $message = 'Invite was sent.';
        if (!$success) {
            $message = 'Invite could not be sent.';
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function pending(Team $team)
    {
        return Invitation::where('team_id', $team->id)->with('role')->get();
    }

    public function inviteDelete(Team $team, Invitation $invitation)
    {
        $message = 'Invite was deleted.';
        $success = false;
        if ($invitation->team_id === $team->id && $invitation->team_id === getSaasTeamId()) {
            $success = $invitation->delete();
        }
        if (!$success) {
            $message = 'Invite could not be deleted.';
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function inviteValidate(Team $team, int $invitationid, string $hash)
    {
        $invitation = Invitation::find($invitationid);
        $valid = ($invitation && sha1($invitation->email) === $hash && $team->id === $invitation->team_id);
        $user_found = false;
        if ($invitation && $valid) {
            if (User::where('email', $invitation->email)->first()) {
                $user_found = true;
            }
        }
        return response()->json([
            'valid' => $valid,
            'user_found' => $user_found,
            'team_name' => $team->name
        ]);
    }

    /**
     * @param Team $team
     * @param int $invitationid
     * @param string $hash
     * @param UserInviteAcceptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptValidate(Team $team, int $invitationid, string $hash, UserInviteAcceptRequest $request)
    {
        $message = 'Success';
        $invitation = Invitation::find($invitationid);
        $valid = ($invitation && sha1($invitation->email) === $hash && $team->id === $invitation->team_id);
        $user_data = $request->get('user', false);
        $user = null;
        if (!$invitation) {
            $message = 'Invite is expired';
        } elseif (!$valid) {
            $message = 'Invite is no longer valid';
        } elseif ($user_data) {
            $user = $this->userRepository->createUserWithoutTeam($user_data, $invitation->email, true);
            if (!$user) {
                $message = 'User creation failed';
            }
        } else {
            $user = User::where('email', $invitation->email)->first();
            if (!$user) {
                $message = 'User does not exist';
            }
        }
        if ($user !== null) {
            $this->userRepository->addUserToTeam($user, $invitation);
        }
        return response()->json([
            'success' => ($user !== null),
            'message' => $message
        ]);
    }

    public function removeUser(Team $team, User $user, Request $request)
    {
        $user->teams()->detach($team->id);
        event(new RemoveUserFromTeam($user->id, $request->get('new_user', null), $team->id));
        return response()->json([
            'success' => true,
            'message' => 'User was removed from the Team'
        ]);
    }

    public function updateGeneral(Team $team, TeamUpdateGeneralRequest $request)
    {
        $this->teamRepository->update($team, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Team was updated'
        ]);
    }

    public function updateBank(Team $team, TeamUpdateBankRequest $request)
    {
        $this->teamRepository->update($team, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Team was updated'
        ]);
    }

    public function current()
    {
        return Auth::user()->teams()->where('id', '=', getSaasTeamId())->first();
    }

    public function teams()
    {
        return Auth::user()->teams;
    }

    /**
     * @param Team $team
     * @return mixed
     */
    public function listTokens(Team $team)
    {
        return $team->tokens;
    }

    /**
     * @param Team $team
     * @return array
     */
    public function createToken(Team $team)
    {
        $accessToken = $team->createToken(\request()->get('token_name'));

        $token = PersonalAccessToken::findToken($accessToken->plainTextToken);
        $token->plain_text_token = $accessToken->plainTextToken;
        $token->save();

        return ['token' => $accessToken->plainTextToken];
    }

    /**
     * @param Team $team
     * @param $tokenId
     * @return bool[]
     */
    public function deleteToken(Team $team, $tokenId)
    {
        $team->tokens()->where('id', $tokenId)->delete();
        return ['success' => true];
    }

    public function create(TeamCreateRequest $request)
    {
        $team = $this->teamRepository->create($request->all(), auth()->user());
        setSaasTeamId($team->id);
        setPermissionsTeamId($team->id);
        return response()->json([
            'success' => true,
            'message' => 'Team was created'
        ]);
    }

    public function uploadLogo(Team $team, Request $request)
    {
        if(!$request->hasFile('logo')) {
            return [
                'success' => false,
                'message' => 'No File given!',
            ];
        }
        $team->clearMediaCollection(Team::MEDIA_LOGO);
        $team->addMediaFromRequest('logo')->toMediaCollection(Team::MEDIA_LOGO);
        return [
            'success' => true,
            'message' => 'Success',
        ];
    }
}
