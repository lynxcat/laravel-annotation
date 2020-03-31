<?php

namespace Lynxcat\Annotation\Command;

use Illuminate\Console\Command;

use Lynxcat\Annotation\Service\Annotation;
use Lynxcat\Annotation\Service\RouteCache;


class AnnotationCacheCommand extends Command
{
    private $annotation;

    private $cache;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annotation:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create annotation route cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->annotation = new Annotation();
        $this->cache = new RouteCache();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $codes = $this->annotation->getCode([base_path(config('annotation.path', 'app/Http/Controllers/')) => config('annotation.namespace', 'App\\Http\\Controllers')]);

        if ($codes['route']) {
            $this->cache->cache($codes['route']);
        }

    }
}
