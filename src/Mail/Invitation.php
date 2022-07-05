<?php

namespace Go2Flow\SaasRegisterLogin\Mail;

use Go2Flow\SaasRegisterLogin\Models\Team\Invitation as InvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invitation extends Mailable
{
    use Queueable, SerializesModels;

    public InvitationModel $invitation;
    public string $acceptUrl;
    public int $team_id;
    public bool $loggable = true;

    public function __construct(
        InvitationModel $invitation,
        string $locale = 'en'
    ) {
        $this->invitation = $invitation;
        $this->locale = $locale;
        $this->team_id = $invitation->team_id;
    }

    public function build()
    {
        $view = 'vendor.srl.mail.team-invitation';
        if (!view()->exists($view)) {
            $view = 'srl::mail.team-invitation';
        }
        $this->acceptUrl = route('srl.team.invite.accept', [
            'team' => $this->invitation->team_id,
            'invitation' => $this->invitation->id,
            'hash' => sha1($this->invitation->email)
        ]);
        return $this->subject(__('Join :team on Courzly!', ['team' => $this->invitation->team->name]))->markdown($view);
    }
}
