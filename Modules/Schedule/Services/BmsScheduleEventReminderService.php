<?php

namespace Modules\Schedule\Services;

use Carbon\Carbon;
use Modules\Schedule\Entities\BmsScheduleEventReminder;
use Illuminate\Support\Facades\Auth;
use Exception;
use Modules\Schedule\Entities\BmsScheduleEvent;

class BmsScheduleEventReminderService
{
    private $bmsScheduleEvent;
    private $bmsScheduleEventReminderEvent;

    public function __construct()
    {
        $this->bmsScheduleEvent = new BmsScheduleEvent();
        $this->bmsScheduleEventReminderEvent = new BmsScheduleEventReminder();
    }

    public function createBmsScheduleEventReminder($reminderInfo){
        $date = Carbon::createFromFormat('Y-m-d', $reminderInfo['date']);
        $time = Carbon::createFromFormat('H:i', $reminderInfo['time']);

        try {
            $user = Auth::user();

            $newBmsEventReminder = $this->saveBmsScheduleEventReminder(
                $reminderInfo['title'],
                $reminderInfo['description'],
                $reminderInfo['client_phone'],
                $reminderInfo['client_name'],
                $reminderInfo['notes'],
                $date,
                $time,
                json_encode($reminderInfo['delay']),
                $reminderInfo['recurrency_type'],
                'active',
                $reminderInfo['client_id'] ?? null,
                !empty($reminderInfo['recurrency_week_days']) ? json_encode($reminderInfo['recurrency_week_days']) : null,
                !empty($reminderInfo['recurrency_week']) ? $reminderInfo['recurrency_week'] : null,
                $user->id
            );

            return $newBmsEventReminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function editBmsScheduleEventReminder($reminderInfo){
        try {
            $user = Auth::user();

            $event = $this->bmsScheduleEvent
                ->where('id' , $reminderInfo['id'])
                ->where('user_id' , $user->id)
                ->with('remembers')
                ->with('reminder')
                ->first();

            if (!$event) {
                throw new Exception('No event found!', 404);
            }

            $reminder = $event->reminder;
            $date = Carbon::createFromFormat('Y-m-d', $reminderInfo['date']);
            $time = Carbon::createFromFormat('H:i', $reminderInfo['time']);

            $this->deleteNextEventsAndRemembers($event, 'canceled');

            $reminder = $this->saveBmsScheduleEventReminder(
                $reminderInfo['title'],
                $reminderInfo['description'],
                $reminderInfo['client_phone'],
                $reminderInfo['client_name'],
                $reminderInfo['notes'],
                $date,
                $time,
                json_encode($reminderInfo['delay']),
                $reminderInfo['recurrency_type'],
                'active',
                $reminderInfo['client_id'] ?? null,
                !empty($reminderInfo['recurrency_week_days']) ? json_encode($reminderInfo['recurrency_week_days']) : null,
                !empty($reminderInfo['recurrency_week']) ? $reminderInfo['recurrency_week'] : null,
                $event->user_id
            );

            $event->update([ 'status' => 'canceled' ]);
            $event->reminder->update(['status' => 'canceled']);

            $currentEvents = $event->remembers()->where('status', 'active')->get();

            if (!$currentEvents->isEmpty()) {
                $currentEvents->each(function ($remember) {
                    $remember->update(['status' => 'canceled']);
                });
            }

            return $reminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function updateBmsScheduleEventReminder($reminder, $reminderInfo, $date, $time){
        $eventUpdate = [];

        $eventUpdate['title'] =  $reminderInfo['title'] ?? $reminder->title;
        $eventUpdate['client_phone'] =  $reminderInfo['client_phone'] ?? $reminder->client_phone;
        $eventUpdate['client_name'] =  $reminderInfo['client_name'] ?? $reminder->client_name;
        $eventUpdate['client_id'] = isset($reminderInfo['client_id']) ? $reminderInfo['client_id'] : $reminder->client_id;
        $eventUpdate['notes'] = $reminderInfo['notes'] ?? $reminder->notes;

        $reminder->update($eventUpdate);

        return $reminder;
    }


    public function saveBmsScheduleEventReminder(
        $title,
        $description,
        $clientPhone,
        $clientName,
        $notes,
        $date,
        $time,
        $delay,
        $recurrencyType,
        $status,
        $clientId,
        $recurrencyWeekDays,
        $recurrencyWeek,
        $userId) {
        try {
            $newBmsEventReminder = BmsScheduleEventReminder::create(
                [
                    'title' => $title,
                    'description' => $description,
                    'client_phone' => $clientPhone,
                    'client_name' => $clientName,
                    'notes' => $notes ,
                    'date' => $date,
                    'time' => $time ,
                    'delay' => $delay ,
                    'recurrency_type' => $recurrencyType ,
                    'status' => $status ,
                    'client_id' => $clientId,
                    'recurrency_week_days' => $recurrencyWeekDays,
                    'recurrency_week' => $recurrencyWeek,
                    'user_id' => $userId
                ]
            );

            if(!$newBmsEventReminder){
                throw new exception('Something went wrong saving a event Reminder!', 500);
            }

            return $newBmsEventReminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getRemindersByUserAndDay($userId, $formattedDate){
        $weekDay = $formattedDate->dayOfWeek;
        $weekMonth = $formattedDate->weekOfMonth;

        return $this->bmsScheduleEventReminderEvent
                ->where('user_id', $userId)
                ->whereJsonContains('recurrency_week_days', $weekDay)
                ->where(function ($query) use ($weekMonth) {
                    $query->whereNull('recurrency_week')
                          ->orWhere('recurrency_week', $weekMonth);
                })
                ->where('date', '<=', $formattedDate->format('Y-m-d'))
                ->where('status', 'active')
                ->get();
    }

    public function removeReminder($eventId, $type) {
        try {
            $user = Auth::user();

            $events = $this->bmsScheduleEvent
                ->where('id', $eventId)
                ->where('user_id', $user->id)
                ->with('remembers')
                ->with('reminder')
                ->get();

            if ($events->isEmpty()) {
                throw new Exception('No event found!', 404);
            }

            if ($type === 'all') {
                $event = $events->first();

                if ($event->parent_id) {
                    $events = $this->bmsScheduleEvent
                        ->where('user_id', $user->id)
                        ->where('parent_id', $event->parent_id)
                        ->whereDate('date', '>=', Carbon::createFromFormat('Y-m-d', $event->date))
                        ->with('remembers')
                        ->with('reminder')
                        ->get();
                } else {
                    $events = $this->bmsScheduleEvent
                        ->where('user_id', $user->id)
                        ->whereDate('date', '>=', Carbon::createFromFormat('Y-m-d', $event->date))
                        ->where('parent_id', $event->id)
                        ->orWhere('id', $event->id)
                        ->with('remembers')
                        ->with('reminder')
                        ->get();
                }

                $event->reminder->update(['status' => 'removed']);
            }

            foreach ($events as $event) {
                $event->update(['status' => 'removed']);

                $event->remembers->each(function ($remember) {
                    $remember->update(['status' => 'removed']);
                });
            }
        } catch (\Exception $e) {
            dd($e);
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function deleteNextEventsAndRemembers($event, $status) {
        $allEventsFromParentId = $this->bmsScheduleEvent
            ->where('bms_schedule_event_reminder_id', $event->bms_schedule_event_reminder_id)
            ->where('status', 'active')
            ->where(function ($query) use ($event) {
                if ($event->parent_id) {
                    $query->where('parent_id', $event->parent_id);
                } else {
                    $query->whereNotNull('parent_id')->where('parent_id', $event->id);
                }
            })
            ->where('user_id', Auth::user()->id)
            ->where(function ($query) use ($event){
                $query->where('date', '>', $event->date)
                    ->orWhere('date', $event->date);
            })
            ->with('remembers')
            ->get();

            if (!empty($allEventsFromParentId)) {
            foreach ($allEventsFromParentId as $nextEvent) {
                $activeRemembers = $nextEvent->remembers()->where('status', 'active')->get();

                if (!$activeRemembers->isEmpty()) {
                    $activeRemembers->each(function ($remember) use ($status) {
                        $remember->update(['status' => $status]);
                    });
                }

                $nextEvent->update(['status' => $status]);
            }
        }
    }
}
