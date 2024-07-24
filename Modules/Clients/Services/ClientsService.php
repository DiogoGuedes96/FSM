<?php

namespace Modules\Clients\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Clients\Entities\Clients;
use Modules\Clients\Entities\ClientAddress;
use Modules\Primavera\Entities\PrimaveraClients;

class ClientsService
{
    /**
     * @var Clients
     */
    protected $bmsClient;

    /**
     * @var PrimaveraClients
     */
    protected $primaveraClients;

    /**
     * @var Clients
     */
    protected $bmsClientAddress;

    public function __construct()
    {
        $this->bmsClient = new Clients();
        $this->primaveraClients = new PrimaveraClients();
        $this->bmsClientAddress = new ClientAddress();
    }

    /**
     * This PHP function retrieves clients by their phone numbers, allowing for multiple phone numbers to
     * be searched at once.
     *
     * @param phones The parameter `` is an array or a string representing one or more phone
     * numbers. The function `getClientsByPhone` retrieves clients from the database whose phone numbers
     * match the ones provided in the `` parameter. If `` is a string, it is converted to an
     * array with
     *
     * @return The function `getClientsByPhone` returns a collection of clients that have at least one of
     * the phone numbers specified in the `` parameter. The clients are retrieved from the database
     * using the `Client` model and the `addresses` relationship is eager loaded.
     */
    public function getClientsByPhone($phones)
    {
        if (!is_array($phones)) {
            $phones = [$phones];
        }

        $clients = $this->bmsClient->where(function ($query) use ($phones) {
            $query->whereIn('phone_1', $phones)
                ->orWhereIn('phone_2', $phones)
                ->orWhereIn('phone_3', $phones)
                ->orWhereIn('phone_4', $phones)
                ->orWhereIn('phone_5', $phones);
        })->with([
            'addresses',
            'originalAddresses' => function ($query) {
                $query->orderBy('id', 'desc');
            }
        ])->get();

        $clientAddress = $this->bmsClientAddress->where(function ($query2) use ($phones) {
            $query2->whereIn('phone', $phones)
                ->orWhereIn('phone_2', $phones);
        })->with('clients')->get();


        if ($clientAddress) {
            foreach ($clientAddress as $address) {
                $address->clients->phone_1 = $address->phone ? $address->phone : $address->clients->phone_1;
                $address->clients->phone_5 = $address->phone_2 ? $address->phone_2 :  $address->clients->phone_5;

                $address->clients->addresses[] = $address->toArray();

                $clients[] = $address->clients;
            }
        }

        return $this->getClientsInvoices($clients);
    }

    public function getClientByPhone($phone)
    {
        if ($phone) {
            $client = $this->bmsClient->where('phone_1', $phone)
                ->orWhere('phone_2', $phone)
                ->orWhere('phone_3', $phone)
                ->orWhere('phone_4', $phone)
                ->orWhere('phone_5', $phone)
                ->orWhereHas('addresses', function ($query) use ($phone) {
                    $query->where('phone', $phone)
                        ->orWhere('phone_2', $phone)
                        ->where('is_contact', true);
                })
                ->with([
                    'addresses' => function ($query) {
                        $query->where('is_contact', true);
                    },
                    'originalAddresses' => function ($query) {
                        $query->orderBy('id', 'desc');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->first();

            return $this->getClientInvoices($client); // Return the client or null if not found
        }
        return null; // If $phone is empty, return null directly
    }

    public function getClientsById($id)
    {
        try {

            $client = $this->bmsClient->find($id);

            if (!$client) {
                throw new exception('No Client Found with the given ID!', 404);
            }

            return $client;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getClientsInvoices($clients) //Used in orders
    {
        if (isset($clients)) {
            $clients->each(function ($client) {
                $primaveraClient = $this->primaveraClients
                    ->where('primavera_id', $client->erp_client_id)
                    ->with('invoices')
                    ->first();

                if ($primaveraClient) {
                    $lastInvoice = $primaveraClient->invoices()
                        ->orderBy('updated_at', 'desc')
                        ->where('doc_type', 'FA')
                        ->whereColumn('total_value', '!=', 'pendent_value')
                        ->first();


                    $primaveraClient->setRelation('last_invoice', $lastInvoice);

                    unset($primaveraClient->invoices);
                }

                $client->primaveraClient = $primaveraClient;

                $invoicesArr = [];

                $today = Carbon::now();
                $thirtyDaysAgo = $today->copy()->subDays(30);
                $sixtyDaysAgo = $today->copy()->subDays(60);
                $ninetyDaysAgo = $today->copy()->subDays(90);

                //Ultimos 30 dias no passado
                $month0 = $this->getInvoicesByPeriod($primaveraClient, $thirtyDaysAgo, $today, 'FA');
                $month1 = $this->getInvoicesByPeriod($primaveraClient, $sixtyDaysAgo, $thirtyDaysAgo->copy()->subDay(), 'FA');
                $month2 = $this->getInvoicesByPeriod($primaveraClient, $ninetyDaysAgo, $sixtyDaysAgo->copy()->subDay(), 'FA');
                //More than 90 days olny invoices with debt
                $month3 = $this->getInvoicesByPeriod($primaveraClient, $ninetyDaysAgo, false, 'FA');

                $clintInvoiceDebt = $this->getClientInvocesDebt($primaveraClient);

                $invoicesArr[0]['months'] = 1;
                $invoicesArr[0]['fa'] = $this->formatEcToListParents($month0);
    
                $invoicesArr[1]['months'] = 2;
                $invoicesArr[1]['fa'] = $this->formatEcToListParents($month1);
    
                $invoicesArr[2]['months'] = 3;
                $invoicesArr[2]['fa'] = $this->formatEcToListParents($month2);
    
                $invoicesArr[3]['months'] = 4;
                $invoicesArr[3]['fa'] = $this->formatEcToListParents($month3);
    
                $client->primaveraClient->invoices = $invoicesArr;
                $client->primaveraClient->sumInvoiceDebt = $clintInvoiceDebt;
            });
        }
        return $clients;
    }

    public function getClientInvoices($client) //Used in calls
    {
        if (isset($client)) {
            $primaveraClient = $this->primaveraClients
                ->where('primavera_id', $client->erp_client_id)
                ->with('invoices')
                ->first();

            if ($primaveraClient) {
                $lastInvoice = $primaveraClient->invoices()
                    ->orderBy('updated_at', 'desc')
                    ->where('doc_type', 'FA')
                    ->whereColumn('total_value', '!=', 'pendent_value')
                    ->first();

                $primaveraClient->setRelation('last_invoice', $lastInvoice);

                unset($primaveraClient->invoices);
            }

            $client->primaveraClient = $primaveraClient;

            $invoicesArr = [];

            $today = Carbon::now();
            $thirtyDaysAgo = $today->copy()->subDays(30);
            $sixtyDaysAgo = $today->copy()->subDays(60);
            $ninetyDaysAgo = $today->copy()->subDays(90);

            //Ultimos 30 dias no passado
            $month0 = $this->getInvoicesByPeriod($primaveraClient, $thirtyDaysAgo, $today, 'FA');
            //De 30 a 60 dias no passado
            $month1 = $this->getInvoicesByPeriod($primaveraClient, $sixtyDaysAgo, $thirtyDaysAgo->copy()->subDay(), 'FA');
            //De 60 a 90 dias
            $month2 = $this->getInvoicesByPeriod($primaveraClient, $ninetyDaysAgo, $sixtyDaysAgo->copy()->subDay(), 'FA');
            //More than 90 days olny invoices with debt
            $month3 = $this->getInvoicesByPeriod($primaveraClient, $ninetyDaysAgo, false, 'FA');

            $clintInvoiceDebt = $this->getClientInvocesDebt($primaveraClient);

            $invoicesArr[0]['months'] = 1;
            $invoicesArr[0]['fa'] = $this->formatEcToListParents($month0);

            $invoicesArr[1]['months'] = 2;
            $invoicesArr[1]['fa'] = $this->formatEcToListParents($month1);

            $invoicesArr[2]['months'] = 3;
            $invoicesArr[2]['fa'] = $this->formatEcToListParents($month2);

            $invoicesArr[3]['months'] = 4;
            $invoicesArr[3]['fa'] = $this->formatEcToListParents($month3);

            $client->primaveraClient->invoices = $invoicesArr;
            $client->primaveraClient->sumInvoiceDebt = $clintInvoiceDebt;
        }
        return $client;
    }

    private function getClientInvocesDebt($primaveraClient)
    {
        return $primaveraClient->invoices()
                    ->where('pendent_value', '>', 0)
                    ->sum('pendent_value');
    }

    private function formatEcToListParents($month)
    {
        $newMonth0 = [];
        foreach ($month as $mon) {
            if (!empty($mon->childrens)) {
                foreach ($mon->childrens as $children) {
                    if (!empty($children)) {
                        array_push($newMonth0, $children);
                    }
                }
            }
        }

        return count($newMonth0) > 0 ? $newMonth0 : $month;
    }

    private function getInvoicesByPeriod($primaveraClient, $from, $to = false, $type = 'EC')
    {
        $query = $primaveraClient->invoices()
            ->where('doc_type', $type)
            ->with('childrens', function ($query) {
                $query->orderBy('created_at', 'desc');
            });
    
        if ($to) {
            $query->whereBetween('invoice_date', [$from, $to]);
        } else {
            $query->where('invoice_date', '<=', $from)
                  ->where(function ($query) {
                      $query->whereNotNull('pendent_value')
                            ->where('pendent_value', '>', 0);
                  });
        }
    
        return $query->get();
    }

    public function getFilteredClients($searchInput)
    {
        $clients = $this->bmsClient->where(function ($query) use ($searchInput) {
            $query->where('phone_1', 'LIKE', $searchInput . '%')
                ->orWhere('phone_2', 'LIKE', $searchInput . '%')
                ->orWhere('phone_3', 'LIKE', $searchInput . '%')
                ->orWhere('phone_4', 'LIKE', $searchInput . '%')
                ->orWhere('phone_5', 'LIKE', $searchInput . '%')
                ->orWhere('name', 'LIKE', '%' . $searchInput . '%')
                ->orWhere('erp_client_id', 'LIKE', $searchInput . '%');
        })->get();

        return $this->getClientsInvoices($clients);
    }
}
