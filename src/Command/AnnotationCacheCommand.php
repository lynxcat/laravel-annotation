<?php

namespace Lynxcat\Annotation\Command;

use Illuminate\Console\Command;

use Lynxcat\Annotation\Scanner;
use Lynxcat\Annotation\AnnotationReader;
use Lynxcat\Annotation\MakeRoute;

class AnnotationCacheCommand extends Command
{

    private $classes = [];
    private $scanner, $reader;

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
        parent::__construct();
        $this->scanner = new Scanner(base_path(config("annotation.path", "app/Http/Controllers/")), config("annotation.namespace","App\\Http\\Controllers"));
        $this->reader = new AnnotationReader();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->scanner->scanAllControllerDir();

        foreach ($this->scanner->getControllers() as $controller){
            $ref = new \ReflectionClass($controller);
            $className = $ref->getName();
            $this->parseClassAnnotation($ref, $className);
            $this->parseMethodsAnnotations($ref, $className);
        }

        $makeRoute = new MakeRoute($this->classes);
        $makeRoute->cache();
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
