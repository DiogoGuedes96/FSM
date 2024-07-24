<?php

namespace Modules\Primavera\Console;

use Illuminate\Console\Command;
use Modules\Primavera\Services\PrimaveraProductsBatchService;
use Modules\Primavera\Services\PrimaveraProductsService;

class PrimaveraUpdateBatchsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'primavera:batchs:updateorcreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the `primavera_batchs` table with information from Primavers`s ERP in the BMS database.';

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
        $batchService   = new PrimaveraProductsBatchService();

        $this->info('Starting product batches update sequence!');
        $batchService->updateProductBatch($this);

        $this->newLine();
        $this->info('Every process was completed succesfully!');
    }
}
