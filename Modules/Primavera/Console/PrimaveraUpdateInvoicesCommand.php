<?php

namespace Modules\Primavera\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Primavera\Services\PrimaveraInvoicesService;
use Illuminate\Support\Facades\Validator;



class PrimaveraUpdateInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'primavera:invoices:updateorcreate {updateType} {startDate} {clientId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the `primavera_invoices` table with information from Primaveras`s ERP.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * If passed only one argument {updateType}:
         * updateType = invoices -> Updates ALL invoices WITHOUT orders. 
         * updateType = orders -> Updates ALL invoices WITH orders. 
         * 
         * If passed argument {startDate}:
         * startDate = latest -> Updates latest invoices WITH OR WITHOUT (combined with updateType) orders (invoices from the last Day). 
         * startDate = year -> Updates invoices WITH OR WITHOUT (combined with updateType) orders since the year passed until the current. 
         * startDate = '' (empty) -> Updates ALL invoices WITH OR WITHOUT (combined with updateType) orders. 
         * 
         * {clintId} is the id from the last client to have his invoices updated.The system will update the invoices from the clientId passed forward.
         */

        $updateType = $this->argument('updateType');
        $startDate = $this->argument('startDate');
        $clientId = $this->argument('clientId');

        $this->info('Starting invoices update sequence!');
        $service = new PrimaveraInvoicesService();
        $endDate   = Carbon::now()->format('Y-m-d');
        if ($startDate) {
            if ($startDate == 'latest') {
                $startDate = Carbon::yesterday()->format('Y-m-d');

                if ($updateType == 'invoices') { //Update latest invoices without orders
                    $this->alert('Updating LATEST invoices!');
                    $this->warn('From ' . $startDate . ' to ' . $endDate);

                    $service->updateOrCreateInvoices($this, $startDate, $endDate);
                } elseif ($updateType == 'orders') { //Update latest invoices with orders
                    $this->alert('Updating LATEST invoices WITH ORDERS!');
                    $this->warn('From ' . $startDate . ' to ' . $endDate);

                    $service->updateOrCreateInvoicesWithOrders($this, $startDate, $endDate, $clientId);
                } else {
                    $this->error('Invalid update Type!');
                    $this->error('Please insert a valid updateType. invoices || orders');
                    die();
                }
            } else { //if $startDate != 'latest'
                try {
                    Carbon::createFromFormat('Y-m-d', $startDate);
                } catch (\Throwable $th) {
                    $startDate = $startDate . '-01-01';
                }

                $validator = Validator::make(['year' => (int)$startDate], [
                    'year' => ['required', 'numeric', 'digits:4', 'between:2000,' . (date('Y'))],
                ]);

                if ($validator->fails()) {
                    $this->error('Invalid year argument!');
                    $this->error('Please insert a valid year between 2000 and ' . (date('Y')));
                    die();
                }

                if ($updateType == 'invoices') { //Update invoices without orders sinse start date
                    $this->alert('Updating invoices since! ' . $startDate . '!');
                    $this->warn('From ' . $startDate . ' to ' . $endDate);

                    $service->updateOrCreateInvoices($this, $startDate, $endDate);
                } elseif ($updateType == 'orders') { //Update invoices with orders sinse start date
                    $this->alert('Updating invoices WITH ORDERS since! ' . $startDate . '!');
                    $this->warn('From ' . $startDate . ' to ' . $endDate);

                    $service->updateOrCreateInvoicesWithOrders($this, $startDate, $endDate, $clientId);
                } else {
                    $this->error('Invalid year update Type!');
                    $this->error('Please insert a valid updateType. invoices || orders');
                    die();
                }
            }
        } else { // if there is not a start date
            if ($updateType == 'invoices') { //Update all invoices 
                $this->alert('Updating All invoices!');

                $service->updateOrCreateInvoices($this);
            } elseif ($updateType == 'orders') { //Update all invoices with orders
                $this->alert('Updating ALL invoices WITH ORDERS!');

                $service->updateOrCreateInvoicesWithOrders($this, null, null, $clientId);
            } else {
                $this->error('Invalid year updateType!');
                $this->error('Please insert a valid updateType. invoices || orders');
                die();
            }
        }

        $this->info('Primavera invoices updated successfully!');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['updateType', InputArgument::REQUIRED, '(WITHOUT STARTDATE) - orders => Updates the latest invoices from primavera with orders., invoices => Updates all invoices from primavera without orders.'],
            ['startDate',  InputArgument::OPTIONAL, 'This argument can be used with "updateType". If passed a startDate, it will update the invoices with or without orders form the date passed until current time. If no date is passed, will update from the last day. If startDate=lates it will update all invoices '],
            ['clientId',   InputArgument::OPTIONAL, 'clintId is the id from the last client to have his invoices updated.The system will update the invoices from the clientId passed forward.'],
        ];
    }
}
