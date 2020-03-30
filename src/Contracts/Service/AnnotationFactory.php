<?php


namespace Lynxcat\Annotation\Contracts\Service;


interface AnnotationFactory
{
    public function start(): AnnotationFactory;

    public function setClasses(array $classes): AnnotationFactory;

    public function setAnnotationClasses(array $classes): AnnotationFactory;

    public function getCode(): array;

    public function getCallable(): array;

}
