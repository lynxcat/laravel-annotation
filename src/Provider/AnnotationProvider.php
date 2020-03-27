<?php
namespace Lynxcat\Annotation\Provider;

use Illuminate\Support\ServiceProvider;
use Lynxcat\Annotation\Scanner;
use Lynxcat\Annotation\AnnotationReader;
use Lynxcat\Annotation\MakeRoute;
use Lynxcat\Annotation\Command\AnnotationCacheCommand;
use Lynxcat\Annotation\Command\AnnotationClearCacheCommand;


class AnnotationProvider extends ServiceProvider
{
    private $classes = [];
    private $scanner, $reader;
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

        if(MakeRoute::isCache()){
            $this->loadRoutesFrom(MakeRoute::loadCache());
        } else{

            $this->scanner = new Scanner(base_path(config('annotation.path', 'app/Http/Controllers/')), config('annotation.namespace', 'App\\Http\\Controllers'));
            $this->reader = new AnnotationReader();


            $this->scanner->scanAllControllerDir();

            foreach ($this->scanner->getControllers() as $controller){
                $ref = new \ReflectionClass($controller);
                $className = $ref->getName();
                $this->parseClassAnnotation($ref, $className);
                $this->parseMethodsAnnotations($ref, $className);
            }
            $makeRoute = new MakeRoute($this->classes);
            $makeRoute->dynamicAddRoute();
        }

        $this->commands([
            AnnotationCacheCommand::class,
            AnnotationClearCacheCommand::class
        ]);
    }

    /**
     * 解析类的注解
     * @param $ref
     * @param $className
     */
    private function parseClassAnnotation($ref, $className){

        $this->classes[$className] = [
            "annotation" => [],
            "method" => []
        ];

        $this->classes[$className]["annotation"] = $this->reader->setDocComment($ref->getDocComment())->parse()->getAnnotations();

    }

    /**
     * 解析方法的注解
     * @param $ref
     * @param $className
     */
    private function parseMethodsAnnotations($ref, $className){
        foreach ($ref->getMethods() as $method){
            $docComment = $method->getDocComment();
            $annotation = $this->reader->setDocComment($docComment)->parse()->getAnnotations();

            if (!empty($annotation)){
                $this->classes[$className]["method"][$method->getName()] = $annotation;
            }
        }

        if (empty($this->classes[$className]["method"])){
            unset($this->classes[$className]);
        }
    }
}
