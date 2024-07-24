<?php

namespace Modules\Schedule\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Schedule\Http\Requests\CreateBmsReminderRequest;
use Modules\Schedule\Http\Requests\ListRemindersByDatesFromUserRequest;
use Modules\Schedule\Http\Requests\ReplaceReminderRequest;
use Modules\Schedule\Services\ReminderDelayService;
use Modules\Schedule\Services\ReminderInfoService;
use Modules\Schedule\Services\ReminderService;
use Illuminate\Support\Facades\Auth;

class BmsReminderController extends Controller
{

    private $reminderService;
    private $reminderInfoService;
    private $reminderDelayService;

    public function __construct()
    {
       $this->reminderService = new ReminderService();
       $this->reminderInfoService = new ReminderInfoService();
       $this->reminderDelayService = new ReminderDelayService();
    }

    public function createBmsReminder(CreateBmsReminderRequest $createBmsReminderRequest)
    {
        try {
            $user = Auth::user();
            $reminderInfo = $this->reminderInfoService->saveNewReminderInfo($createBmsReminderRequest->reminder_info);
            $reminder = $this->reminderService->createNewReminder($createBmsReminderRequest, $reminderInfo->id, $user->id);
            
            if ($createBmsReminderRequest->reminder_delay) {
                $this->reminderDelayService->saveNewReminderDelay($reminder->id, $createBmsReminderRequest->reminder_delay);
            }

            $reminder = $this->reminderService->getReminderById($reminder->id);
            return response()->json(['reminder' => $reminder]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function getReminderDetails($reminderId)
    {
        try {
            $reminder = $this->reminderService->getReminderById($reminderId);

            return response()->json(['reminder' => $reminder]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function listTodayRemindersFromUser($userId)
    {
        try {
            $reminderList = $this->reminderService->getTodayRemindersFromUser($userId);

            return response()->json(['reminders' => $reminderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function listAllRemindersFromUser($userId)
    {
        try {
            $reminderList = $this->reminderService->getAllRemindersFromUser($userId);

            return response()->json(['reminders' => $reminderList]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function listRemindersByDatesFromUser(ListRemindersByDatesFromUserRequest $listRemindersByDatesFromUserRequest)
    {
        try {
            $user = Auth::user();

            $dates = $listRemindersByDatesFromUserRequest->dates;
            $reminders = $this->reminderService->getRemindersByDatesFromUser($user->id, $dates);
        
            return response()->json(['reminders' => $reminders]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function replaceReminder(ReplaceReminderRequest $replaceReminderRequest)
    {
        try {
            $user = Auth::user();

            $oldReminderId = $replaceReminderRequest->reminder_id;
            $newReminder = $this->reminderService->replaceReminder($oldReminderId);
            $this->reminderInfoService->updateReminderInfo($oldReminderId, $replaceReminderRequest->reminder_info, $user->id)->id;
            $this->reminderService->softDeleteReminder($oldReminderId);

            $reminder = $this->reminderService->getReminderById($newReminder->id);
            return response()->json(['reminder' => $reminder]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 500);
        }
    }

    public function softDeleteReminder($id)
    {
        try {
            $this->reminderService->softDeleteReminder($id);
            return response()->json(['message' => 'Success', 'Success' => 'Reminder with id "'.$id.'" deleted successfuly!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function listCurrentMinuteReminders()
    {
        try {
            $userId = Auth::user()->id;
            $reminders = $this->reminderService->getRemindersInCurrentMinute($userId);
            return response()->json(['reminder' => $reminders]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 500);
        }
    }
}
