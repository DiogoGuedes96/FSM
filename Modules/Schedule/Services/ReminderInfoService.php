<?php

namespace Modules\Schedule\Services;

use Exception;
use Modules\Clients\Services\ClientsService;
use Modules\Schedule\Entities\BmsReminderInfo;

class ReminderInfoService
{
    private $bmsReminderInfo;
    private $bmsClientService;
    private $reminderService;

    public function __construct()
    {
        $this->bmsReminderInfo = new BmsReminderInfo();
        $this->bmsClientService = new ClientsService();
        $this->reminderService = new ReminderService();
    }

    public function saveNewReminderInfo($reminderInfoRequest){
        try {
            if(isset($reminderInfoRequest['client_id'])){
                $bmsClient = $this->bmsClientService->getClientsById($reminderInfoRequest['client_id']);
            }

            $newReminderInfo = BmsReminderInfo::create(
                [
                    'name'           => $reminderInfoRequest['name'] ?? null,
                    'date'           => $reminderInfoRequest['date'] ?? null ,
                    'time'           => $reminderInfoRequest['time'] ?? null ,
                    'remember_time'  => $reminderInfoRequest['remember_time'] ?? null ,
                    'remember_label' => $reminderInfoRequest['remember_label'] ?? null ,
                    'client_name'    => $reminderInfoRequest['client_name'] ?? null ,
                    'client_phone'   => $reminderInfoRequest['client_phone'] ?? null ,
                    'client_id'      => $bmsClient->id ?? null ,
                    'notes'          => $reminderInfoRequest['notes'] ?? null ,
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

    public function getReminderInfoById($id, $relations = null)
    {
        try {
            if (!$id) {
                throw new Exception('The given ReminderInfo ID was invalid.', 422);
            }

            if ($relations) {
                return $this->bmsReminderInfo->with($relations)->where('id', $id)->first();
            }

            $bmsReminderInfo = $this->bmsReminderInfo
                ->where('id', $id)
                ->first();

            if(!$bmsReminderInfo){
                throw new Exception('No reminder found with the given ID !', 404);
            }

            return $bmsReminderInfo;
        } catch (Exception $e){
            throw new exception($e->getMessage(), $e->getCode());
        }
    }

    public function updateReminderInfo($reminderId, $request, $userId, $newReminderInfo = false){
        try {
            $reminderInfoRequest = $request->reminder_info;

            if(isset($reminderInfoRequest['client_id'])){
                $bmsClient = $this->bmsClientService->getClientsById($reminderInfoRequest['client_id']);
            }

            $updateData = [];

            if (!empty($reminderInfoRequest['name'])) {
                $updateData['name'] = $reminderInfoRequest['name'];
            }

            if (!empty($reminderInfoRequest['date'])) {
                $updateData['date'] = $reminderInfoRequest['date'];
            }

            if (!empty($reminderInfoRequest['time'])) {
                $updateData['time'] = $reminderInfoRequest['time'];
            }

            if (!empty($reminderInfoRequest['remember_time'])) {
                $updateData['remember_time'] = $reminderInfoRequest['remember_time'];
            }

            if (!empty($reminderInfoRequest['remember_label'])) {
                $updateData['remember_label'] = $reminderInfoRequest['remember_label'];
            }

            if (!empty($reminderInfoRequest['remember_frequency'])) {
                $updateData['remember_frequency'] = $reminderInfoRequest['remember_frequency'];
            }

            if (!empty($reminderInfoRequest['client_name'])) {
                $updateData['client_name'] = $reminderInfoRequest['client_name'];
            }

            if (!empty($reminderInfoRequest['client_phone'])) {
                $updateData['client_phone'] = $reminderInfoRequest['client_phone'];
            }

            if (!empty($bmsClient)) {
                $updateData['client_id'] = $bmsClient->id;
            }

            if (!empty($reminderInfoRequest['notes'])) {
                $updateData['notes'] = $reminderInfoRequest['notes'];
            }

            if (empty($updateData)) {
                throw new exception('No data was given!', 422);
            }

            $reminder = $this->reminderService->getReminderById($reminderId);
            $reminderId = $reminder->id;

            if ($newReminderInfo) {
                $bmsReminderInfo = new BmsReminderInfo($updateData);
                $bmsReminderInfo->save();
                
                $reminderId = 
                    $this->reminderService->createNewReminder($request, $bmsReminderInfo->id, $userId, $reminder->id)->id;
            } else {
                $reminder->reminderInfo()->update($updateData);
            } 

            if(!$reminderId){
                throw new exception('Something went Wrong creating a Reminder!', 404);
            }

            return $reminderId;
        } catch (Exception $e) {
            throw new exception($e->getMessage(), $e->getCode());
        }
    }
}
