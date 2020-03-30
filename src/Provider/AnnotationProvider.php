<?php
namespace Lynxcat\Annotation\Provider;

use Illuminate\Support\ServiceProvider;
use Lynxcat\Annotation\Service\RouteCache;
use Lynxcat\Annotation\Command\AnnotationCacheCommand;
use Lynxcat\Annotation\Command\AnnotationClearCacheCommand;

use Lynxcat\Annotation\Service\Annotation;


class AnnotationProvider extends ServiceProvider
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../Config/annotation.php' => config_path('annotation.php'),
        ]);


        if(RouteCache::isCache()){
            $this->loadRoutesFrom(RouteCache::loadCache());
        } else{
            $annotation = new Annotation();
            $annotation->run([base_path(config('annotation.path', 'app/Http/Controllers/')) => config('annotation.namespace', 'App\\Http\\Controllers')]);
        }

        if ($this->app->runningInConsole()){
            $this->commands([
                AnnotationCacheCommand::class,
                AnnotationClearCacheCommand::class
            ]);
        }
    }
}
