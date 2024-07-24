<?php

namespace Modules\Calls\Services;

use Illuminate\Contracts\Validation\Validator;
use Modules\Calls\Entities\AsteriskCall;
use Modules\Calls\Entities\AsteriskEvent;
use Modules\Calls\Entities\AsteriskCredentials;
use Modules\Primavera\Services\PrimaveraClientsService;
use \PAMI\Message\Event\EventMessage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as PaginationClass;
use Modules\Calls\Entities\PhoneBlackList;
use Modules\Clients\Services\ClientsService;
use Throwable;


class AsteriskService
{

    private $asteriskCall;
    private $asteriskEvent;
    private $asteriskCredentials;

    /**
     * @var ClientsService
     */
    private $clientsService;


    public function __construct()
    {
        $this->asteriskCall = new AsteriskCall();
        $this->asteriskEvent = new AsteriskEvent();
        $this->asteriskCredentials = new AsteriskCredentials();
        $this->clientsService = new ClientsService();
    }

    /**
     * It saves the event to the database
     *
     * @param EventMessage event The name of the event.
     */
    public function saveAsteriskEvent(EventMessage $event)
    {
        $newEvent = [
            'uniqueid'      => strval($event->getKey('uniqueid')),
            'linkedid'      => strval($event->getKey('linkedid')),
            'type'          => strval($event->getKey('event')),
            'channel'       => strval($event->getKey('channel')),
            'channel_state' => strval($event->getKey('channelState')),
            'event_json'    => $event->getRawContent()
        ];

        $this->asteriskEvent->create($newEvent);
    }

    /**
     * It Handles an asterisk Call
     * Saves or updates a call
     * Calls the function to save the event in the database
     *
     * @param EventMessage event The event name.
     */
    public function handleAsteriskCall(EventMessage $event, $command)
    {
        try {
            switch ($event->getName()) {
                case 'Newchannel':
                    if ((!empty(strlen($event->getKey('calleridnum'))) && strlen($event->getKey('calleridnum')) < 9) || (!empty(strlen($event->getKey('Reg_calleenum'))) && strlen($event->getKey('Reg_calleenum')) < 9) ) {
                        return false;
                    }

                    if ($event->getKey('Context') == 'from-internal') {
                        $command->error(now() . 'Internal call ', $event->getRawContent());
                    }


                    $newCall = [
                        'caller_phone' => strval($event->getKey('calleridnum')),
                        'linkedid'     => strval($event->getKey('linkedid')),
                        'status'       => 'ringing',
                        'callee_phone' => strlen(strval($event->getKey('Reg_calleenum'))) >= 1 ? strval($event->getKey('Reg_calleenum')) : null,
                        'client_name'  => strval($event->getKey('connectedlinename')),
                        'hangup_status' => 0,
                        'viewed' => 1,
                    ];

                    $this->asteriskCall->create($newCall);

                    $this->saveAsteriskEvent($event);
                    $command->info(now() . " " . $event->getName() .  ": " . strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                    break;
                case 'Newstate':
                
                    if ((!empty(strlen($event->getKey('calleridnum'))) && strlen($event->getKey('calleridnum')) < 9) || (!empty(strlen($event->getKey('Reg_calleenum'))) && strlen($event->getKey('Reg_calleenum')) < 9) ) {
                        return false;
                    }

                    if ($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        $call->update([
                            'status' => 'connected',
                            'viewed' => 1,
                            ]);
                            
                        if (strlen(strval($event->getKey('calleridnum'))) > 8) {
                            $call->update(['caller_phone' => strval($event->getKey('calleridnum'))]);
                        }
                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now() . " " . $event->getName() .  ": " . strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                    break;
                case 'Hangup':
                    if ($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        if (
                            $call->status == 'ringing' || //If the call was ringing but never picked up (status = connected) then it was a missed call
                            in_array($call->status, ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603'])  || //If the call has one of this codes it was a missed call 
                            in_array($event->getKey('Cause'), ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603']) ) {
                            $call->update(
                                [
                                    'status' => 'missed',
                                    'viewed' => 0 ,
                                    'hangup_status' => $event->getKey('Cause')
                                ]
                            );
                        } else {
                            $call->update(
                                [
                                    'status' => 'hangup',
                                    'viewed' => 1,
                                    'hangup_status' => $event->getKey('Cause')
                                ]
                            );

                            if (strlen(strval($event->getKey('calleridnum'))) > 8) {
                                $call->update(
                                    [
                                        'caller_phone' => strval($event->getKey('calleridnum'))
                                    ]
                                );
                            }
                        }
                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now() . " " . $event->getName() .  ": " . strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                    break;
                case 'Dial':
                    if ($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        $call->update([
                                'status' => 'Dial',
                                'viewed' => 1,
                            ]);

                        if (strlen(strval($event->getKey('calleridnum'))) > 8) {
                            $call->update(['caller_phone' => strval($event->getKey('calleridnum'))]);
                        }

                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now() . " " . $event->getName() .  ": " . strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                    break;
                case 'Hold':
                    if ($call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first()) {
                        $call->update([
                                'status' => 'Hold',
                                'viewed' => 1,
                            ]);

                        if (strlen(strval($event->getKey('callee_phone'))) >= 1) {
                            if (!$call->callee_number) {
                                $call->update(['callee_phone' =>  strval($event->getKey('Reg_calleenum'))]);
                            }
                        }

                        $this->updateCallePhoneNumber($call, $event, $command);
                    }

                    $this->saveAsteriskEvent($event);
                    $command->info(now() . " " . $event->getName() .  ": " . strval($event->getKey('calleridnum')) . " " . "Destination: " . strval($event->getKey('Reg_calleenum')) ?? 'null');
                    break;
                default:
                    $call = $this->asteriskCall->where('linkedid', $event->getKey('Linkedid'))->first();
                    $this->updateCallePhoneNumber($call, $event, $command);

                    break;
            }
        } catch (Throwable $th) {
            $command->error(now() . 'Error on saving event: ', $th);
        }
    }

    public function updateCallePhoneNumber($call, $event, $command)
    {
        try {
            if ($call) {
                if (strlen(strval($event->getKey('Reg_calleenum'))) >= 8) {
                    if (!$call->callee_number) {
                        $call->update(['callee_phone' =>  strval($event->getKey('Reg_calleenum'))]);
                    }
                }
            }
        } catch (Throwable $th) {
            $command->error(now() . 'Error on saving event: ', $th);
        }
    }

    /**
     * Get all calls in progress, and get the clients on Primavera for each call.
     *
     * @return The calls in progress.
     */
    public function getCallsInProgress()
    {
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $calls = $this->asteriskCall
            ->whereIn('status', ['connected', 'ringing'])
            ->where(function ($query) use ($blacklistPhones) {
                $query->where('callee_phone', null)
                    ->orWhereNotIn('callee_phone', $blacklistPhones)
                    ->where('callee_phone', '!=', 'caller_phone');
            })
            ->get();

        return $this->getClientsByPhone($calls);
    }

    /**
     * It ends all calls that are currently connected or ringing
     */
    public function endAllCalls()
    {
        $calls = $this->asteriskCall->whereIn('status', ['connected', 'ringing'])->get();

        foreach ($calls as $call) {
            $call->update(['status' => 'hangup']);
        }
    }

    /**
     * It gets all the calls in progress, and then it gets all the clients from Primavera that have the
     * same phone number as the calls in progress
     *
     * @param calls an array of objects with the following properties:
     *
     * @return An array of objects with the call and client information.
     */
    public function getClientsByPhone($calls)
    {
        $callsToReturn = [];

        foreach ($calls as $key => $call) {
            $phone = $call->callee_phone ?? $call->caller_phone;
            if ($phone) {
                $client = $this->clientsService->getClientByPhone($phone);
                $push = false;
                if ($client) {
                    $callsToReturn[] = ['call' => $call, 'client' => $client, 'key' => $key];
                    $push = true;
                }

                if (!$push) {
                    array_push($callsToReturn, ['call' => $call, 'client' => null, 'key' => $key]);
                }
            }
        }

        if ($calls instanceof PaginationClass) {
            $pagination = [
                'currentPage' => $calls->currentPage(),
                'perPage' => $calls->perPage(),
                'total' => $calls->total(),
                'lastPage' => $calls->lastPage(),
            ];

            return ['calls' => $callsToReturn, 'pagination' => $pagination];
        }
        return $callsToReturn;
    }

    /**
     * It gets all the calls that are connected or hangup, orders them by date and then gets the clients on
     * Primavera
     *
     * @return The calls that are connected or hangup.
     */
    public function getCallsHangup()
    {
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $calls = $this->asteriskCall->whereNotIn('status', ['Hold, connected', 'ringing'])
            ->where(function ($query) use ($blacklistPhones) {
                $query->where('callee_phone', null)
                    ->orWhereNotIn('callee_phone', $blacklistPhones)
                    ->where('callee_phone', '!=', 'caller_phone');
            })

            ->orderby('created_at', 'DESC')
            ->paginate(15);

        return $this->getClientsByPhone($calls);
    }

    /**
     * It gets all the calls that were missed, and then it gets the clients on Primavera
     *
     * Codes when cll is hangup without picking the phone
     * 17: User busy
     * 18: No user response
     * 19: No answer
     * 21: Call rejected
     * 22: Number changed
     * 32: No Circuit/Channel Available
     * 34: Circuit/channel congestion
     * 42: Switching equipment congestion
     * 480: Temporarily Unavailable
     * 487: Request Terminated
     * 600: Busy Everywhere
     * 603: Decline
     *
     * @return The function getCallsMissed() is returning the calls that were missed.
     */
    public function getCallsMissed($count = false)
    {
        $blacklistPhones = PhoneBlackList::pluck('phone');

        $calls = $this->asteriskCall
            ->where(function ($query) {
                $query->whereIn('status', ['missed'])
                ->orWhereIn('hangup_status', ['17', '18', '19', '21', '22', '32', '34', '42', '480', '487', '600', '603']);
            })
            ->where(function ($query) use ($blacklistPhones) {
                $query->where('callee_phone', null)
                    ->orWhereNotIn('callee_phone', $blacklistPhones)
                    ->where('callee_phone', '!=', 'caller_phone');
      
            });
           
        if($count){
            $calls = $calls->where('viewed', 0)->count();

            return $calls;
        }else{
            $calls = $calls->orderby('created_at', 'DESC')->paginate(15);
            
            return $this->getClientsByPhone($calls);
        }
    }

    /**
     * It returns the Asterisk credentials if the password is correct
     *
     * @param Request request The request object
     *
     * @return The current credentials are being returned.
     */
    public function asteriskCredentialsIndex(Request $request)
    {
        $currentCredentials = $this->asteriskCredentials->first();

        if (!$currentCredentials) {
            return response()->json(['message' => 'No Credentials Found', 'error' => "Currently there are no credentials!"], 404);
        }

        if ($request->internal_pw != $currentCredentials->internal_pw) {
            return response()->json(['message' => 'Please insert the correct password', 'error' => "The password inserted is wrong"], 401);
        }

        return $currentCredentials->makeHidden('internal_pw');
    }

    /**
     * It updates the asterisk credentials
     *
     * @param Request request The request object.
     *
     * @return the response of the request.
     */
    public function asteriskCredentialsUpdate(Request $request)
    {
        $currentCredentials = $this->asteriskCredentials->first();
        if ($currentCredentials) {
            if ($request->internal_pw != $currentCredentials->internal_pw) {
                return response()->json(['message' => 'Please insert the correct password', 'error' => "The password inserted is wrong"], 401);
            }

            $currentCredentials->update($request->all());
        } else {
            $this->asteriskCredentials->create($request->all());
        }

        $newCredentials = $this->asteriskCredentials->first();

        return  response()->json(['Success!' => 'Credentials Updated', 'New Credentials' => $newCredentials->makeHidden('internal_pw')], 200);
    }

    public function setViewed($calls){
        $this->asteriskCall
            ->whereIn('id', $calls)
            ->update(['viewed' => 1]);
    }
}
