<?php

namespace Gause\ImageableLaravel\Console\Commands;

use Illuminate\Console\Command;

class ImportImages extends Command
{
    protected $signature = 'image:import';

    protected $description = 'Imports images from given resource to app storage and DB.';

    public function handle()
    {
       // TODO 
    }
}