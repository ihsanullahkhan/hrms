<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use \App\Helpers\Traits\TenantAble;
use Illuminate\Support\Facades\Http;

class AttendancePunchInController extends Controller
{
    use TenantAble;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    public function checkPunchIn()
    {
        /** @var User $user */
        $user = auth()->user();

        return $this->service
            ->setModel($user)
            ->checkPunchIn();
    }

    public function getPunchInTime()
    {
        /** @var User $user */
        $user = auth()->user();

        $this->service
            ->setModel($user)
            ->validatePunchOut();

        return AttendanceDetails::getUnPunchedOut($user->id);
    }

    public function getGeolocation()
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $geoSetting = resolve(SettingRepository::class)->getFormattedSettings(
            'geolocation', $setting_able_type, $setting_able_id
        );

        return array_merge(
            config('settings.ip_geolocation_endpoints'),
             $geoSetting
        );
    }
}
