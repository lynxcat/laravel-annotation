<?php


namespace Lynxcat\Annotation\Service;


use Lynxcat\Annotation\Contracts\Service\AnnotationFactory;

class AnnotationFactoryImpl implements AnnotationFactory
{
    private $annotationClasses;
    private $classes;
    private $annotations;

    public function setClasses(array $classes): AnnotationFactory
    {
        $this->classes = $classes;
        return $this;
    }

    public function start(): AnnotationFactory
    {
        $annotations = [];
        foreach ($this->annotationClasses as $class) {
            array_push($annotations, new $class);
        }
        $this->annotations = $annotations;
        return $this;
    }

    public function setAnnotationClasses(array $classes): AnnotationFactory
    {
        $this->annotationClasses = $classes;
        return $this;
    }


    public function getCode(): array
    {
        $code = [];
        foreach ($this->classes as $class) {
            foreach ($this->annotations as $annotation) {
                if ($class->getType() == $annotation->getType()) {
                    $code[$class->getType()] = $code[$class->getType()] ?? "";
                    $annotation->setModel($class);
                    $code[$class->getType()] .= $annotation->getCode();
                }
            }
        }
        return $code;
    }

    public function getCallable(): array
    {
        $callable = [];
        foreach ($this->classes as $class) {
            foreach ($this->annotations as $annotation) {
                if ($class->getType() == $annotation->getType()) {
                    $callable[$class->getType()] = $callable[$class->getType()] ?? [];
                    $annotation->setModel($class);
                    array_push($callable[$class->getType()], $annotation->getCallable());
                }
            }
        }

        return $callable;
    }
}
