<?php

namespace Modules\Calls\Services;

use Exception;
use Modules\Calls\Entities\AsteriskCall;
use Modules\Calls\Entities\AsteriskEvent;
use Modules\Calls\Entities\AsteriskCredentials;
use \PAMI\Message\Event\EventMessage;
use Illuminate\Pagination\LengthAwarePaginator as PaginationClass;
use Modules\Clients\Services\ClientsService;
use Throwable;


class CallsService
{

    private $asteriskCall;

    public function __construct()
    {
        $this->asteriskCall = new AsteriskCall();
    }

    /**
     * It saves the event to the database
     * 
     * @param EventMessage event The name of the event.
     */
    public function terminateCall($callId)
    {
        $asteriskCall = $this->asteriskCall->where('id', $callId)->first();

        if(!$asteriskCall){
            throw new Exception('No call was found with the given id!', 404);
        }

        if ($asteriskCall && ($asteriskCall->status === 'connected' || $asteriskCall->status === 'ringing')) {
            $asteriskCall->update([
                'status' => 'hangup',
                'hangup_status' => 16
            ]);
            
            return;
        }
        throw new Exception('Incorrectly trying to terminate a Call !', 400);
    }
}
