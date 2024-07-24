<?php

namespace Modules\Schedule\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Schedule\Entities\BmsReminderDelay;
use Modules\Schedule\Http\Requests\DelayBmsReminderRequest;
use Modules\Schedule\Services\ReminderDelayService;
use Modules\Schedule\Services\ReminderService;

class BmsReminderDelayController extends Controller
{
    protected $reminderDelay;
    private $reminderDelayService;
    private $reminderService;

    public function __construct()
    {
        $this->reminderDelay = new BmsReminderDelay();
        $this->reminderDelayService = new ReminderDelayService();
        $this->reminderService = new ReminderService();
    }

    public function delayReminder(DelayBmsReminderRequest $delayBmsReminderRequest)
    {
        try {
            $this->reminderDelayService->saveNewReminderDelay(intval($delayBmsReminderRequest->reminder_id), $delayBmsReminderRequest->reminder_delay);
            $reminder = $this->reminderService->getReminderById(intval($delayBmsReminderRequest->reminder_id));

            return response()->json(['reminder' => $reminder]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
