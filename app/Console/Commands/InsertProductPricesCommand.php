<?php

namespace Modules\Products\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InsertProductPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'insert:product-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to populate table bms_product_prices to insert default prices';

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
        $products = DB::table('bms_products')->get();
    
        foreach ($products as $product) {
            DB::table('bms_product_prices')->insert([
                'bms_product_id' => $product->id,
                'unit' => $product->sell_unit,
                'price' => $product->avg_price,
                'default' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        $this->info('Product prices inserted successfully.');
    }
    

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
