<?php

namespace Lynxcat\Annotation\Command;

use Illuminate\Console\Command;
use Lynxcat\Annotation\Service\RouteCache;

class AnnotationClearCacheCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annotation:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clear annotation route cache';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        RouteCache::clearCache();
    }
}
