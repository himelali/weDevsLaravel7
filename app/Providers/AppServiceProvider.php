<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Activity::saving(function (Activity $activity) {
            $agent = new Agent();
            $platform = $agent->platform();
            $browser = $agent->browser();
            $browser = trim($browser . ' ' . $agent->version($browser));
            $platform = trim($platform . ' ' . $agent->version($platform));

            $device = null;
            if($agent->isDesktop())
                $device = 'Desktop: '.$agent->device();
            elseif($agent->isPhone())
                $device = 'Phone: '.$agent->device();
            elseif($agent->isRobot())
                $device = 'Robot: '.$agent->robot();

            $activity->ip_address = request()->ip();
            $activity->browser = $browser ?: null;
            $activity->platform = $platform ?: null;
            $activity->device = $device;
        });
    }
}
