<?php

namespace Modules\Schedule\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Schedule\Services\BmsScheduleEventRememberService;

class BmsScheduleEventRememberController extends Controller
{
    private $bmsSchduleEventRememberService;

    public function __construct()
    {
       $this->bmsSchduleEventRememberService = new BmsScheduleEventRememberService();
    }

    public function listCurrentMinuteBmsScheduleEventsRemembers($unread = false)
    {
        $unread = filter_var($unread, FILTER_VALIDATE_BOOLEAN);

        try {
            $user = Auth::user();

            $eventRemembers = $this->bmsSchduleEventRememberService->getCurrentMinuteEventRemembersFromUser($user->id, $unread);

            return response()->json(['remembers' => $eventRemembers]);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function setDone($eventId)
    {
        try {
            $this->bmsSchduleEventRememberService->setDone($eventId);

            return response()->json(['message' => 'done']);
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }
}
