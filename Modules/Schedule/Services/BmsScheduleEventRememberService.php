<?php

namespace Modules\Schedule\Services;

use Carbon\Carbon;
use Modules\Schedule\Entities\BmsScheduleEventRemember;

use Exception;

class BmsScheduleEventRememberService
{
    private $bmsScheduleEventRemember;

    public function __construct()
    {
        $this->bmsScheduleEventRemember = new BmsScheduleEventRemember();
    }

    public function saveBmsScheduleEventRemember($date, $time, $status, $bmsScheduleEventId){
        try {
            $bmsEvent = $this->bmsScheduleEventRemember
                            ->where('date', $date)
                            ->where('time', $time)
                            ->where('bms_schedule_event_id', $bmsScheduleEventId)
                            ->first();

            if($bmsEvent){
                return;
            }

            $newBmsEvent = BmsScheduleEventRemember::create(
                [
                    'date'                  => $date,
                    'time'                  => $time ,
                    'status'                => $status ,
                    'bms_schedule_event_id' => $bmsScheduleEventId ,
                ]
            );

            if(!$newBmsEvent){
                throw new exception('Something went wrong saving a event Remember!', 500);
            }

        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getRememberById($rememberId){
        return $this->bmsScheduleEventRemember->where('id', $rememberId)->with('scheduleEvent')->first();
    }

    public function getCurrentMinuteEventRemembersFromUser($userId, $unread){
        try {
            $today = Carbon::now()->format('Y-m-d');
            $currentTime = Carbon::now()->format('H:i:s');

            $eventRemembers = $this->bmsScheduleEventRemember
                ->where('status', 'active')
                ->where(function ($query) use ($today, $currentTime) {
                    $query->whereDate('date', '<', $today)
                        ->orWhere(function ($subQuery) use ($today, $currentTime) {
                            $subQuery->whereDate('date', $today)
                            ->whereTime('time', '<=', $currentTime);
                        });
                })
                ->whereHas('scheduleEvent', function ($query) use ($userId) {
                    $query->whereNotNull('user_id')
                        ->where('user_id', $userId)
                        ->whereHas('reminder'); 
                })
                ->with(['scheduleEvent' => function ($query) use ($userId) {
                    $query->whereNotNull('user_id')
                        ->where('user_id', $userId)
                        ->with('reminder');
                }])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

                $response = clone $eventRemembers;
                
            if($unread){ // Only returns when are unread reminders to that time
                $unreadEventRemembers = $eventRemembers->filter(function ($item) {
                    return $item->read_at === null;
                });
                
                if ($unreadEventRemembers->isNotEmpty()) { //If there is unread Events
                    $this->markScheduleEventsAsRead($eventRemembers);
                    return $response;
                } else {
                    return [];
                }
            }

            $this->markScheduleEventsAsRead($eventRemembers);

            return $response; //Returns always all reminders to that time
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function markScheduleEventsAsRead($eventRemembers) {
        try {
            foreach ($eventRemembers as $eventRemember) {
                $this->bmsScheduleEventRemember->where('id', $eventRemember->id)->update(['read_at' => now()]);
            }
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getEventRemembersByEventId($eventId){
        try {
            $this->bmsScheduleEventRemember->where('bms_schedule_event_id', $eventId)->where('status', 'active');
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function setDone($eventId){
        try {
            $this->bmsScheduleEventRemember->where('id', $eventId)->update(['status' => 'done']);
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function createScheduleEventRemember($reminder, $event, $date){
        try {
            $date            = Carbon::parse($date);
            $formattedDate   = $date->format('Y-m-d');
            $baseTime        = $reminder->time;

            if ($reminder->time instanceof Carbon) {
                $baseTime        = $reminder->time;
            } else {
                $baseTime        = Carbon::createFromFormat('H:i:s', $reminder->time);
            }

            $delays = json_decode($reminder->delay);
            if(!empty($delays)){
                foreach ($delays as $delay) {
                    $time = $baseTime->copy()->subMinutes($delay)->format('H:i');
                    $this->saveBmsScheduleEventRemember($formattedDate, $time, 'active', $event->id);
                }
            }
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
}
