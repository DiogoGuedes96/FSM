<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

class RemoveComposerOnModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:composer-r {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run rm -r vendor for a specific module';

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
        $module = $this->argument('module');
        $this->info('Getting Module directory: '.$module);
        if(!chdir(Module::getModulePath($module))){
            $this->error('Something went Wrong going to the module directory');
        }else{
            $this->info('Removing composer packages for module: '.$module);
            $output = null;
            $code   = null;
            
            exec('rm -r vendor', $output, $code);

            if ($code){
                $this->error('Something went Wrong');
                $this->comment('Output:');
                foreach($output as $line){
                    $this->comment($line);
                }
                $this->error('Code: '.$code);
            }else{
                $this->info('Composer packages for module '.$module.' removed successfully');
                $this->info('Output:');
                foreach($output as $line){
                    $this->comment($line);
                }
            }
        }
    }
}
