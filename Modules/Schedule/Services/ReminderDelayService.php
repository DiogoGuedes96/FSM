<?php

namespace Modules\Schedule\Services;

use Exception;
use Modules\Schedule\Entities\BmsReminderDelay;

class ReminderDelayService
{
    private $bmsReminderDelay;

    public function __construct()
    {
        $this->bmsReminderDelay = new BmsReminderDelay();
       
    }

    public function saveNewReminderDelay($bmsReminderId, $reminderDelayRequest){
        try {

            $newReminderInfo = BmsReminderDelay::create(
                [
                    'time_delay'           => $reminderDelayRequest['time_delay'] ?? null,
                    'remember_time_delay'  => $reminderDelayRequest['remember_time_delay'] ?? null ,
                    'remember_label_delay' => $reminderDelayRequest['remember_label_delay'] ?? null ,
                    'date'                 => $reminderDelayRequest['date'] ?? null ,
                    'bms_reminder'         => $bmsReminderId
                ]
            );

            if(!$newReminderInfo){
                throw new exception('Something went Wrong creating a Reminder!', 404);
            }

            return $newReminderInfo;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
}
