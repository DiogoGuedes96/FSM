<?php

namespace Modules\Schedule\Services;

use Carbon\Carbon;
use Modules\Schedule\Entities\BmsScheduleEvent;
use Modules\Schedule\Entities\BmsScheduleEventReminder;
use Modules\Schedule\Services\BmsScheduleEventRememberService;
use Exception;
use Illuminate\Support\Facades\Auth;

class BmsScheduleEventService
{
    private $bmsScheduleEvent;
    private $bmsScheduleEventReminder;
    private $bmsScheduleEventRememberService;
    private $bmsScheduleEventReminderService;

    public function __construct()
    {
        $this->bmsScheduleEvent = new BmsScheduleEvent();
        $this->bmsScheduleEventReminder = new BmsScheduleEventReminder();
        $this->bmsScheduleEventRememberService = new BmsScheduleEventRememberService();
        $this->bmsScheduleEventReminderService = new BmsScheduleEventReminderService();
    }

    public function saveBmsScheduleEvent($date, $time, $status, $userId, $parentId, $reminderId){
        try {
            $newBmsEvent = BmsScheduleEvent::create(
                [
                    'date'                 => $date,
                    'time'                 => $time ,
                    'status'               => $status ,
                    'user_id'              => $userId ,
                    'parent_id'            => $parentId,
                    'bms_schedule_event_reminder_id' => $reminderId
                ]
            );

            if(!$newBmsEvent){
                throw new exception('Something went wrong saving a event !', 500);
            }

            return $newBmsEvent;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getScheduleEventsById($eventId){
        try {
            $event = $this->bmsScheduleEvent
                        ->where('id' , $eventId)
                        ->with('remembers', 'reminder')
                        ->first();

            if(!$event){
                throw new Exception('No events found!', 404);
            }

            return $event;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getScheduleEventsByDatesFromUser($userId, $dates){
        try {
            foreach ($dates as $date) {
                if ($date instanceof Carbon) {
                    $formattedDate = $date->format('Y-m-d');
                } else {
                    $formattedDate = Carbon::createFromFormat('Y-m-d', $date);
                }

                $reminders = $this->bmsScheduleEventReminderService->getRemindersByUserAndDay($userId, $formattedDate);

                foreach ($reminders as $reminder) {
                    if($reminder->recurrency_type == 'yearly'){
                        $datesToCreate = $this->predictAnualDates( $date, $reminder->date, 1 );
                    }else {
                        $datesToCreate = $this->predictNextDates(
                            json_decode($reminder->recurrency_week_days), $reminder->recurrency_week, $date, 0
                        );
                    }

                    $this->generateEventsAndRemembers( $reminder, $datesToCreate );
                }
            }

            $scheduleEventsByDate = [];
            foreach ($dates as $date) {
                $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
                $scheduleEvents = $this->bmsScheduleEvent
                    ->where('user_id', $userId)
                    ->where('date', $carbonDate->format('Y-m-d'))
                    ->where('status', 'active')
                    ->with('reminder')
                    ->orderBy('time', 'asc')
                    ->get();

                $scheduleEventsByDate[$carbonDate->format('Y-m-d')] = $scheduleEvents->toArray();
            }

            if(!$scheduleEventsByDate){
                throw new Exception('No events found!', 404);
            }

            return $scheduleEventsByDate;
        } catch (Exception $e) {
            dd($e);
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getWeekDaysByDate($date, $countDays = 7){
        $date          = Carbon::parse($date);
        $weekDay       = $date->dayOfWeek;
        $initalDate    = $date->copy()->subDays($weekDay);

        $nextDates = [];

        for ($i=1; $i <= $countDays; $i++) {
            $nextDates[] = $initalDate->copy()->addDays($i)->format('Y-m-d');
        }

        return $nextDates;
    }

    public function createScheduleEvents($reminder, $date, $parent = null){
        try {

            if ($date instanceof Carbon) {
                $formattedDate = $date->format('Y-m-d');
            } else {
                $formattedDate = Carbon::createFromFormat('Y-m-d', $date);
            }

            $date = Carbon::parse($formattedDate);

            $event = $this->bmsScheduleEventReminder
                ->where('id', $reminder->id)
                ->whereHas('scheduleEvent', function ($query) use ($formattedDate) {
                    $query ->where('date', $formattedDate);
                    // ->where('status', 'active')
                })->first();

            if($event){
                return;
            }

            $event = $this->bmsScheduleEvent
                ->where('bms_schedule_event_reminder_id', $reminder->id)
                ->where('status', 'active')
                ->whereNull('parent_id')
                ->first();

            if($event){
                $parent = $event;
            }

            $user = Auth::user();

            return $this->saveBmsScheduleEvent(
                $date,
                $reminder->time,
                'active',
                $user->id,
                $parent ? $parent->id : $event,
                $reminder->id
            );
        } catch (Exception $e) {
            dd($e);
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function predictNextDates($recurrencyWeekDays, $reccurencyWeek, $date, $numberOfDates = 1){
        try {
            $nextDates     = [];
            $date          = Carbon::parse($date);
            $weekDay       = $date->dayOfWeek;
            $initalDate    = $date->copy()->subDays($weekDay);

            for($i = 0; $i <= $numberOfDates; $i++) {
                foreach ($recurrencyWeekDays as $day) {
                    $currentDate = $initalDate->copy()->addDays($day);

                    if (!$this->isDateGteThenCurrentDate($currentDate)) {
                        continue;
                    }

                    if($reccurencyWeek){
                        if ($reccurencyWeek == $currentDate->weekOfMonth) {
                            array_push($nextDates, $currentDate);
                        }
                        continue;
                    }

                    array_push($nextDates, $currentDate);
                }
                $initalDate = $initalDate->next('Sunday');
            }

            return $nextDates;
        } catch (Exception $e) {
            dd($e);
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function isDateGteThenCurrentDate($date){
        $inputDate = Carbon::parse($date)->startOfDay(); // Set time to 00:00:00
        $currentDate = Carbon::now()->startOfDay();      // Set time to 00:00:00

        return $inputDate->gte($currentDate);
    }

    public function predictAnualDates($date, $originDate, $numberOfDates = 1){
        try {
            $nextDates  = [];
            $date       = Carbon::parse($date);
            $originDate = Carbon::parse($originDate);
            $weekDay    = $date->dayOfWeek;
            $sunday     = $date->copy()->subDays($weekDay);

            for($i = 0; $i <= $numberOfDates; $i++) {
                $currentDate = $sunday->copy()->addDays($i);

                if($currentDate->format('md') == $originDate->format('md')){
                    array_push($nextDates, $currentDate);
                }
            }

            return $nextDates;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function generateEventsAndRemembers($eventReminder, $dates) {
        try {
            foreach ($dates as $date) {
                $newBmsEvent = $this->createScheduleEvents($eventReminder, $date);

                if ($newBmsEvent) {
                    $this->bmsScheduleEventRememberService->createScheduleEventRemember($eventReminder, $newBmsEvent, $date);
                }
            }
        } catch (\Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function findNextDaysToNewEvent($eventReminder){
        try {
            if ($eventReminder->recurrency_type == 'yearly') {
                $dates = $this->predictAnualDates(
                    $eventReminder->date,
                    $eventReminder->date, 2
                );
            } else if ($eventReminder->recurrency_type != 'never') {
                $dates = $this->predictNextDates(
                    json_decode($eventReminder->recurrency_week_days),
                    $eventReminder->recurrency_week,
                    $eventReminder->date, 5
                );
            } else {
                $dates = [
                    $eventReminder->date
                ];
            }

            return $dates;
        } catch (\Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function addDelayToEvent($eventId, $delay) {
        try {
            $remember = $this->bmsScheduleEventRememberService->getRememberById($eventId);

            if (!$remember) {
                throw new Exception('Event not found!', 404);
            }

            $remember->scheduleEvent->remembers->each(function ($remember) {
                $remember->update(['status' => 'rescheduled']);
            });

            $newtime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $remember->scheduleEvent->date . $remember->scheduleEvent->time
            )->addMinutes($delay);

            $remember->scheduleEvent->update([
                'date' => $newtime->format('Y-m-d'),
                'time' => $newtime->format('H:i')
            ]);

            $this->bmsScheduleEventRememberService->saveBmsScheduleEventRemember(
                $newtime->format('Y-m-d'),
                $newtime->format('H:i'),
                'active',
                $remember->scheduleEvent->id,
            );

        } catch (\Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
}
