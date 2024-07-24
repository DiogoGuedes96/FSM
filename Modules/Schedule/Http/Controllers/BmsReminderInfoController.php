<?php

namespace Modules\Schedule\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Schedule\Entities\BmsReminderInfo;
use Modules\Schedule\Http\Requests\EditReminderInfoRequest;
use Modules\Schedule\Services\ReminderInfoService;
use Modules\Schedule\Services\ReminderService;

class BmsReminderInfoController extends Controller
{
    protected $reminderInfo;
    private $reminderService;
    private $reminderInfoService;

    public function __construct()
    {
        $this->reminderInfo = new BmsReminderInfo();
        $this->reminderService = new ReminderService();
        $this->reminderInfoService = new ReminderInfoService();
    }

    public function editReminderInfo(EditReminderInfoRequest $request)
    {
        try {
            $user = Auth::user();
            $reminderId = intval($request->reminder_id);

            $reminderId = $this->reminderInfoService->updateReminderInfo(
                intval($request->reminder_id), $request, $user->id, $request->new_reminder_info
            );

            return response()->json(['reminder' => $this->reminderService->getReminderById($reminderId)]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
