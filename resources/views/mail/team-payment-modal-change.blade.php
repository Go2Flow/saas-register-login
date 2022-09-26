@component('mail::message')
@php
    /* @var \Go2Flow\SaasRegisterLogin\Models\Team $team */
@endphp
Teamname: {{$team->name}}
Team id: {{$team->id}}
PSP Id: {{$team->psp_id}}
PSP Instanz: {{$team->psp_instance}}
Neues Modell: {{$team->payment_model}}
@endcomponent
