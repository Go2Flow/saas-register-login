<?php

namespace Go2Flow\SaasRegisterLogin\Mail;

use Go2Flow\SaasRegisterLogin\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentModelChange extends Mailable
{
    use Queueable, SerializesModels;

    public Team $team;
    public int $team_id;
    public bool $loggable = true;

    public function __construct(
        Team $team,
    ) {
        $this->team = $team;
        $this->team_id = $team->id;
    }

    public function build()
    {
        $view = 'vendor.srl.mail.team-payment-modal-change';
        if (!view()->exists($view)) {
            $view = 'srl::mail.team-payment-modal-change';
        }
        return $this->subject('Bezahlmodell Änderung angefragt')->markdown($view);
    }
}
