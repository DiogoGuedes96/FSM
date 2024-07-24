<?php

namespace Modules\Primavera\Console;

use Illuminate\Console\Command;
use Modules\Primavera\Services\PrimaveraClientsService;

class PrimaveraUpdateClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'primavera:clients:updateorcreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the `primavera_clients` table with information from Primavers`s ERP in the BMS database.';

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
        $this->info('Starting clients update sequence!');
        $service = new PrimaveraClientsService();

        $service->updateOrCreateClients($this);

        $this->info('Primavera clients updated succesfully.');
    }
}
