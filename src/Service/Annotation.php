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


    public function run(array $pathNamespaces, object $app): void
    {
        $classes = $this->boot($pathNamespaces);

        //make callable
        $callable = $this->annotationFactory->setAnnotationClasses([
            RouteAnnotationImpl::class,
            ServiceAnnotationImpl::class
        ])->setClasses($classes)->start()->getCallable();

        //call route function
        if (isset($callable['route'])) {
            foreach ($callable['route'] as $fn) {
                call_user_func($fn);
            }
        }

        //call service function
        if (isset($callable['service'])) {
            foreach ($callable['service'] as $fn) {
                call_user_func($fn, $app);
            }
        }
    }

    public function getCode(array $pathNamespaces): array
    {
        $classes = $this->boot($pathNamespaces);

        //create code
        return $this->annotationFactory->setAnnotationClasses([
            RouteAnnotationImpl::class,
            ServiceAnnotationImpl::class
        ])->setClasses($classes)->start()->getCode();
    }

    public function boot(array $pathNamespaces): array
    {
        //scan files
        foreach ($pathNamespaces as $path => $namespace) {
            $this->scanner->setPathAndNamespace($path, $namespace)->scan();
        }
        $files = $this->scanner->getFiles();

        //parse annotations
        return $this->readerFactory->setFiles($files)->setReaderClasses([
            RouteReaderImpl::class,
            ServiceReaderImpl::class
        ])->start()->getAnnotationsClass();
    }
}
