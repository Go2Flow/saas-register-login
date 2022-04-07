<?php

namespace Go2Flow\SaasRegisterLogin\Http\Controllers\API;

use Go2Flow\SaasRegisterLogin\Http\Controllers\Controller;
use Go2Flow\SaasRegisterLogin\Models\Team;
use Go2Flow\SaasRegisterLogin\Models\Team\Invitation;
use Go2Flow\SaasRegisterLogin\Repositories\PermissionRepositoryInterface;
use Go2Flow\SaasRegisterLogin\Repositories\TeamRepositoryInterface;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    private PermissionRepositoryInterface $permissionRepository;
    private TeamRepositoryInterface $teamRepository;

    public function __construct(
        PermissionRepositoryInterface $permissionRepository,
        TeamRepositoryInterface $teamRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->teamRepository = $teamRepository;
    }

    public function users()
    {
        $team = currentTeam();
        if ($team) {
            return currentTeam()->users;
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
}
