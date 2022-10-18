<?php

use Go2Flow\SaasRegisterLogin\Models\Team;
use Carbon\Carbon;

if (! function_exists('setSaasTeamId')) {
    /**
     * @param int|string|\Illuminate\Database\Eloquent\Model $id
     *
     */
    function setSaasTeamId($id)
    {
        session()->put('team_id', $id);
    }
}

if (! function_exists('getSaasTeamId')) {
    /**
     * @return int|null
     */
    function getSaasTeamId(): ?int
    {
        return session()->get('team_id', null);
    }
}

if (! function_exists('currentTeam')) {
    /**
     * @return Team|null
     */
    function currentTeam(): ?Team
    {
       return Team::find(getSaasTeamId());
    }

}

if (! function_exists('timezoneBeforeSave')) {
    function timezoneBeforeSave($datetime, $timezone = null)
    {
        if ($datetime === null) {
            return null;
        }
        if (!$timezone) {
            $timezone = getTeamTimezone();
        }
        $timestamp = Carbon::parse($datetime)->format('Y-m-d H:i:s');
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $timezone);

        return $date->setTimezone('UTC');
    }
}
if (! function_exists('timezoneBeforeRead')) {
    function timezoneBeforeRead($datetime, $timezone = null)
    {
        if ($datetime === null) {
            return null;
        }
        if (!$timezone) {
            $timezone = getTeamTimezone();
        }
        $timestamp = Carbon::parse($datetime)->format('Y-m-d H:i:s');
        $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, 'UTC')->setTimezone($timezone)->format('Y-m-d H:i:s');
        return Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, 'UTC');
    }
}
if (! function_exists('getTeamTimezone')) {
    function getTeamTimezone()
    {
        $timezone = session()->get(getSaasTeamId().'_time_zone', null);
        if (!$timezone) {
            /** @var Team $team */
            $team = Team::findOrFail(getSaasTeamId());
            $timezone = $team->time_zone;
            session()->put(getSaasTeamId().'_time_zone', $timezone);
        }
        return $timezone;
    }
}
