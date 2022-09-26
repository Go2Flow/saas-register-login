**Installation**

1. Add TeamsPermission::class to your Middleware Array of Laravel
2. Add srl:team_clean_invites command to your App\Console\Kernel (you can run it often eg. every 5 minutes)
3. Add srl:team_update_kyc_status command to your App\Console\Kernel (you can run it semi often eg. every hour)
