<?php


namespace Lynxcat\Annotation\Service;


use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;
use Lynxcat\Annotation\Contracts\Model\Files;
use Lynxcat\Annotation\Contracts\Service\Reader;
use Lynxcat\Annotation\Contracts\Service\ReaderFactory;
use Mockery\Exception;

/**
 * @Service
 * Class ReaderFactoryImpl
 * @package Lynxcat\Annotation\Service
 */
class ReaderFactoryImpl implements ReaderFactory
{

    private $files;

    private $classes;

    private $annotationsClass = [];

    public function setFiles(Files $files): ReaderFactory
    {
        $this->files = $files;
        return $this;
    }


    public function setReaderClasses(array $classes): ReaderFactory
    {
        $this->classes = $classes;
        return $this;
    }

    private function addAnnotationsClass(AnnotationsClass $annotationsClass)
    {
        array_push($this->annotationsClass, $annotationsClass);
    }

    public function getAnnotationsClass(): array
    {
        return $this->annotationsClass;
    }

    public function start(): ReaderFactory
    {
        foreach ($this->files->getFiles() as $file => $cls) {
            try {
                $ref = new \ReflectionClass($cls);
                foreach ($this->classes as $class) {
                    $reader = new $class;
                    $annotationsClass = $reader->parse($ref);
                    if (!empty($annotationsClass->getAnnotations()) || !empty($annotationsClass->getMethodAnnotations())) {
                        $this->addAnnotationsClass($annotationsClass);
                    }
                }
            } catch (\ReflectionException $e) {
                //do nothing.
            }
        }
        return $this;
    }
}
