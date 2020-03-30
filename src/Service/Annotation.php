<?php


namespace Lynxcat\Annotation\Service;


class Annotation
{
    private $scanner;
    private $readerFactory;
    private $annotationFactory;

    public function __construct()
    {
        $this->scanner = new ScannerImpl();
        $this->readerFactory = new ReaderFactoryImpl();
        $this->annotationFactory = new AnnotationFactoryImpl();
    }


    public function run($pathNamespaces){
        //扫描文件
        foreach ($pathNamespaces as $path => $namespace){
            $this->scanner->setPathAndNamespace($path, $namespace)->scan();
        }
        $files = $this->scanner->getFiles();
        //解析注解
        $classes = $this->readerFactory->setFiles($files)->setReaderClasses([
            RouteReaderImpl::class,
            ServiceReaderImpl::class
        ])->start()->getAnnotationsClass();

        //根据注解生成回调函数
        $callable = $this->annotationFactory->setAnnotationClasses([
            RouteAnnotationImpl::class,
        ])->setClasses($classes)->start()->getCallable();

        foreach ($callable['route'] as $callable){
            call_user_func($callable);
        }
    }

    public function getCode($pathNamespaces): array {
        //扫描文件
        foreach ($pathNamespaces as $path => $namespace){
            $this->scanner->setPathAndNamespace($path, $namespace)->scan();
        }
        $files = $this->scanner->getFiles();

        //解析注解
        $classes = $this->readerFactory->setFiles($files)->setReaderClasses([
            RouteReaderImpl::class,
            ServiceReaderImpl::class
        ])->start()->getAnnotationsClass();

        //根据注解生成代码
        $code = $this->annotationFactory->setAnnotationClasses([
            RouteAnnotationImpl::class,
        ])->setClasses($classes)->start()->getCode();

        return $code;
    }
}
