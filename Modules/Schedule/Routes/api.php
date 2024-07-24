<?php

use Illuminate\Http\Request;
use Modules\Schedule\Http\Controllers\BmsReminderController;
use Modules\Schedule\Http\Controllers\BmsReminderDelayController;
use Modules\Schedule\Http\Controllers\BmsReminderInfoController;

use Modules\Schedule\Http\Controllers\BmsScheduleEventController;
use Modules\Schedule\Http\Controllers\BmsScheduleEventReminderController;
use Modules\Schedule\Http\Controllers\BmsScheduleEventRememberController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/schedule', function (Request $request) {
    return $request->user();
});

Route::prefix('schedule')->middleware('auth:sanctum')->group(function () {
    Route::prefix('reminder')->group(function () {
        Route::post('/create', [BmsReminderController::class, 'createBmsReminder']);
        Route::get('/details/{bms_reminder}', [BmsReminderController::class, 'getReminderDetails']);
        Route::get('/listAll/{user_id}', [BmsReminderController::class, 'listAllRemindersFromUser']);
        Route::post('/listByDates', [BmsReminderController::class, 'listRemindersByDatesFromUser']);
        Route::get('/listToday/{user_id}', [BmsReminderController::class, 'listTodayRemindersFromUser']);
        Route::post('/delay', [BmsReminderDelayController::class, 'delayReminder']);
        Route::put('/edit', [BmsReminderInfoController::class, 'editReminderInfo']);
        Route::post('/replace', [BmsReminderController::class, 'replaceReminder']);
        Route::delete('/delete/{bms_reminder}', [BmsReminderController::class, 'softDeleteReminder']);
        Route::get('/listCurrentMinute', [BmsReminderController::class, 'listCurrentMinuteReminders']);
    });
});

Route::prefix('schedule')->middleware('auth:sanctum')->group(function () {
    Route::prefix('event')->group(function () {
        Route::prefix('reminder')->group(function () {
            Route::post('create', [BmsScheduleEventReminderController::class, 'createEventReminder']);
            Route::put('edit', [BmsScheduleEventReminderController::class, 'editEventReminder']);
            Route::delete('delete/{eventId}/{type}', [BmsScheduleEventReminderController::class, 'softDeleteBmsScheduleEvent']);
        });
        Route::prefix('remember')->group(function () {
            Route::put('done/{eventId}', [BmsScheduleEventRememberController::class, 'setDone']);
            Route::get('listCurrentMinute/{unread?}', [BmsScheduleEventRememberController::class, 'listCurrentMinuteBmsScheduleEventsRemembers'])
                ->name('listCurrentMinute');
            Route::post('delay', [BmsScheduleEventController::class, 'delayBmsScheduleEvent']);
        });
        // Route::get('/details/{event_id}', [BmsScheduleEventController::class, 'getBmsScheduleEventsDetails']);
        Route::post('listByDates', [BmsScheduleEventController::class, 'listEventsByDatesFromUser']);
    });
});
