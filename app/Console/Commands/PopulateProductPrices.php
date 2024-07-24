<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:product-prices';

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
     * @return int
     */
    public function handle()
    {
        $products = DB::table('bms_products')->get();
    
        foreach ($products as $product) {
            DB::table('bms_product_prices')->insert([
                'bms_product_id' => $product->id,
                'unit' => $product->sell_unit,
                'price' => $product->last_price,
                'default' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        $this->info('Product prices inserted successfully.');
    }
}
