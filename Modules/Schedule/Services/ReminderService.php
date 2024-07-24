<?php

namespace Modules\Schedule\Services;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Modules\Schedule\Entities\BmsReminder;

class ReminderService
{
    private $user;
    private $bmsReminder;

    public function __construct()
    {
        $this->user        = new User();
        $this->bmsReminder = new BmsReminder();
    }

    public function createNewReminder($reminderInfoRequest, $bmsReminderInfoId, $userId, $reminderParent = null){
        try {
            if (!$this->user->where('id', $reminderInfoRequest['user_id'])) {
                throw new exception('The given userId is not valid !', 404);
            }

            $newReminder = $this->saveNewReminder(
                $reminderInfoRequest['week_days'] ?? null,
                $reminderInfoRequest['month_day'] ?? null,
                $reminderInfoRequest['year_day'] ?? null,
                $reminderInfoRequest['start_date'] ?? null,
                true,
                $userId ?? null,
                $bmsReminderInfoId,
                $reminderParent ?? null,
            );

            if(!$newReminder){
                throw new exception('Something went wrong saving a reminder !', 500);
            }

            return $newReminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function replaceReminder($bmsReminderId){
        try {

            $bmsReminder = $this->bmsReminder->where('id', $bmsReminderId)->first();

            $newReminder = $this->saveNewReminder(
                json_decode($bmsReminder->week_days) ?? null,
                $bmsReminder->month_day ?? null,
                $bmsReminder->year_day ?? null,
                $bmsReminder->start_date ?? null,
                true,
                $bmsReminder->user_id ?? null,
                $bmsReminder->bms_reminder_info,
            );

            if(!$newReminder){
                throw new exception('Something went wrong saving a reminder !', 500);
            }

            return $newReminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function saveNewReminder($weekDays, $monthDay, $yearDay, $startDate, $active, $userId, $bmsReminderInfoId, $reminderParent = null){
        try {
            $newReminder = BmsReminder::create(
                [
                    'week_days'         => json_encode($weekDays),
                    'month_day'         => $monthDay ,
                    'year_day'          => $yearDay ,
                    'start_date'        => $startDate ,
                    'active'            => $active ,
                    'user_id'           => $userId ,
                    'bms_reminder_info' => $bmsReminderInfoId,
                    'reminder_parent'   => $reminderParent
                ]
            );
            
            if(!$newReminder){
                throw new exception('Something went wrong saving a reminder !', 500);
            }

            return $newReminder;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getReminderById($id, $relations = null)
    {
        try {
            if (!$id) {
                throw new Exception('The given Reminder ID was invalid.', 422);
            }

            if ($relations) {
                return $this->bmsReminder->with($relations)->where('id', $id)->first();
            }

            $bmsReminder = $this->bmsReminder
                ->with('reminderInfo')
                ->with('delays')
                ->where('id', $id)
                ->first();

            if(!$bmsReminder){
                throw new Exception('No reminder found with the given ID !', 404);
            }

            return $bmsReminder;
        } catch (Exception $e){
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getTodayRemindersFromUser($userId){
        try {
            $today = [Carbon::now()->format('d-m-Y')];
            $reminders = $this->getRemindersByDatesFromUser($userId, $today);

            if(!$reminders){
                throw new Exception('No reminders found!', 404);
            }

            return $reminders;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getRemindersByDatesFromUser($userId, $dates){
        try {
            $remindersByDate = [];
            foreach ($dates as $date) {
                $formattedDate = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');

                $reminders = $this->bmsReminder->where(function ($query) use ($formattedDate) {
                    $query->where(function ($query) use ($formattedDate) {
                        $query->where('year_day', Carbon::parse($formattedDate)->dayOfYear)
                            ->orWhere('month_day', Carbon::parse($formattedDate)->day)
                            ->orWhereJsonContains('week_days', Carbon::parse($formattedDate)->dayOfWeek + 1);
                    })
                    ->orWhereDate('start_date', $formattedDate);
                })
                ->where('active', true)
                ->where('user_id', $userId)
                ->with('reminderInfo', 'delays')
                ->get();
        
                $remindersByDate[$date] = $reminders;
            }
        
            if(!$remindersByDate){
                throw new Exception('No reminders found!', 404);
            }

            //encontrar os lembretes que foram eliminados e remover dos dias seguintes.
            // $remindersToRemove = [];

            // foreach ($remindersByDate as $date => $reminderByDate) {
            //     foreach ($reminderByDate as $reminder) {
            //         if($reminder->created_at){
            //             $eventDay = Carbon::createFromFormat('d-m-Y', $date);

            //             dd($formattedDate);


            //             $remindersToRemove[] = $reminder;
            //         }
            //     }
            // }

            return $remindersByDate;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function getAllRemindersFromUser($userId, $relations = null){
        try {
            if (!$userId) {
                throw new Exception('The given user ID was invalid.', 422);
            }

            if ($relations) {
                return $this->bmsReminder->with($relations)->where('user_id', $userId)->first();
            }
            
            $reminders = $this->bmsReminder
                ->with(['reminderInfo', 'delays'])
                ->where('user_id', $userId)
                ->get();

            if(!$reminders){
                throw new Exception('No reminders found!', 404);
            }

            return $reminders;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function softDeleteReminder($reminderId){
        try {
            if (!$this->bmsReminder->where('id', $reminderId)->exists()) {
                throw new Exception('No reminders found with given ID!', 404);
            }

            $reminder = $this->bmsReminder->where('id', $reminderId)->first();
            $reminder->update( [ 'deleted_at' => Carbon::now() ] );
            
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
    public function getRemindersInCurrentMinute($userId)
    {
        try {
            // $date = Carbon::now()->tz('Europe/Lisbon')->format('Y-m-d');
            $date = Carbon::now()->format('Y-m-d');

            $todayReminders = $this->bmsReminder->where(function ($query) use ($date) {
                $query->where(function ($query) use ($date) {
                    $query->where('year_day', Carbon::parse($date)->dayOfYear)
                        ->orWhere('month_day', Carbon::parse($date)->day)
                        ->orWhereJsonContains('week_days', Carbon::parse($date)->dayOfWeek + 1);
                })
                ->orWhereDate('start_date', $date);
            })
            ->where('user_id', $userId)
            ->with('reminderInfo', 'delays')
            ->get();

            // $currentMinute = Carbon::now()->tz('Europe/Lisbon')->format('H:i:00');
            $currentMinute = Carbon::now()->format('H:i:00');
            $reminders = [];

            foreach ($todayReminders as $reminder) {      
                if ($reminder->delays->contains(function ($delay) use ($date, $currentMinute) {
                    return ($delay->date == $date) &&
                           ($delay->time_delay == $currentMinute || $delay->remember_time_delay == $currentMinute);
                })) {
                    array_push($reminders, $reminder);
                    continue;
                }

                if ($reminder->reminderInfo->remember_time == $currentMinute || $reminder->reminderInfo->time == $currentMinute) {
                    array_push($reminders, $reminder);
                }
            }
            return $reminders;

        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
}
