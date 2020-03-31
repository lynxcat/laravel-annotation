<?php

namespace Lynxcat\Annotation\Provider;

use Illuminate\Support\ServiceProvider;
use Lynxcat\Annotation\Service\Cache;
use Lynxcat\Annotation\Command\AnnotationCacheCommand;
use Lynxcat\Annotation\Command\AnnotationClearCacheCommand;

use Lynxcat\Annotation\Service\Annotation;


class AnnotationProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
    }


    public function register()
    {
        if (config("annotation.serviceIsOpen") && Cache::isServiceCache()) {
            require_once Cache::loadServiceCache();
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../Config/annotation.php' => config_path('annotation.php'),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../Config/annotation.php', "annotation");

        if (Cache::isRouteCache()) {
            $this->loadRoutesFrom(Cache::loadRouteCache());
        } else {
            $annotation = new Annotation();
            $params = [base_path(config('annotation.path')) => config('annotation.namespace')];

            if (config('annotation.serviceIsOpen')) {
                $params[base_path(config('annotation.servicePath'))] = config('annotation.serviceNamespace');
            }

            $annotation->run($params, $this->app);
        }


        if ($this->app->runningInConsole()) {
            $this->commands([
                AnnotationCacheCommand::class,
                AnnotationClearCacheCommand::class
            ]);
        }
    }
}
