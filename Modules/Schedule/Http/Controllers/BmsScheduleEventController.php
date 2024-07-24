<?php

namespace Modules\Schedule\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Schedule\Http\Requests\ListEventsByDatesFromUserRequest;
use Modules\Schedule\Services\BmsScheduleEventService;

class BmsScheduleEventController extends Controller
{
    private $bmsSchduleEventService;

    public function __construct()
    {
       $this->bmsSchduleEventService = new BmsScheduleEventService();
    }

    public function listEventsByDatesFromUser(ListEventsByDatesFromUserRequest $listEventsByDatesFromUserRequest)
    {
        try {
            $user = Auth::user();

            if (!empty($listEventsByDatesFromUserRequest->dates)) {
                $dates = $listEventsByDatesFromUserRequest->dates;
            } else {
                $dates = $this->bmsSchduleEventService->getWeekDaysByDate(Carbon::now());
            }

            $scheduleEvents = $this->bmsSchduleEventService->getScheduleEventsByDatesFromUser($user->id, $dates);

            return response()->json(['events' => $scheduleEvents]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function delayBmsScheduleEvent(Request $request) {
        $event = $this->bmsSchduleEventService->addDelayToEvent($request->event_id, $request->delay);
        return response()->json(['events' => $event]);
    }


}
