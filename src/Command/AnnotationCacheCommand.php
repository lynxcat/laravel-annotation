<?php

namespace Lynxcat\Annotation\Command;

use Illuminate\Console\Command;

use Lynxcat\Annotation\Service\Annotation;
use Lynxcat\Annotation\Service\Cache;


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
        $this->cache = new Cache();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $params = [base_path(config('annotation.path')) => config('annotation.namespace')];

        if (config('annotation.serviceIsOpen')) {
            $params[base_path(config('annotation.servicePath'))] = config('annotation.serviceNamespace');
        }

        $codes = $this->annotation->getCode($params);

        if (isset($codes['route'])) {
            $this->cache->routeCache($codes['route']);
        }

        if (isset($codes['service'])) {
            $this->cache->serviceCache($codes['service']);
        }
    }
}
