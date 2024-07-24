<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;
use Throwable;

class ModuleController extends Controller
{
    public function index()
    {
        return view('modules.index', ['modules' => Module::all(), 'output' => '']);
    }

    public function installModuleNpm($moduleName)
    {
        try{
            Artisan::call('module:npm-i', ['module' => $moduleName]);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   
        }
    }

    public function removeModuleNpm($moduleName)
    {
        try{
            Artisan::call('module:npm-r', ['module' => $moduleName]);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   
        }
    }

    public function installModulecomposer($moduleName)
    {
        try{
            Artisan::call('module:composer-i', ['module' => $moduleName]);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   
        }
    }

    public function removeModuleComposer($moduleName)
    {
        try{
            Artisan::call('module:composer-r', ['module' => $moduleName]);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   
        }
    }

    public function enableModule($moduleName)
    {
        try{
            Module::enable($moduleName);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   

        }
    }

    public function disableModule($moduleName)
    {
        try{
            Module::disable($moduleName);
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output()]);
        }catch(Throwable $th){
            return view('modules.index', ['modules' => Module::all(), 'output' => Artisan::output(), $th]);   
        }
    }
}
