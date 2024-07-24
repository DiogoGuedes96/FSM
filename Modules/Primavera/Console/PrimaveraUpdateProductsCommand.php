<?php

namespace Modules\Primavera\Console;

use Illuminate\Console\Command;
use Modules\Primavera\Services\PrimaveraProductsBatchService;
use Modules\Primavera\Services\PrimaveraProductsService;

class PrimaveraUpdateProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'primavera:products:updateorcreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates the `primavera_products` table with information from Primavers`s ERP in the BMS database.';

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
        $this->info('Starting products update sequence!');
        $this->info('Starting products update sequence!');

        $productService = new PrimaveraProductsService();

        $this->info('Prodcut Updates started!');
        $productService->updateProducts($this);
        $this->info('Primavera products updated succesfully.');
        $this->newLine();

        $batchService   = new PrimaveraProductsBatchService();

        $this->info('Starting product batches update sequence!');
        $batchService->updateProductBatch($this);

        $this->newLine();
        $this->info('Every process was completed succesfully!');
    }
}
