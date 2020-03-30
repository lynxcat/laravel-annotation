<?php


namespace Lynxcat\Annotation\Contracts\Model;


interface AnnotationsClass
{
    public function setType(string $type);

    public function getType(): string;

    public function setClassName(string $name);

    public function getClassName(): string;

    public function setClassNamespace(string $name);

    public function getClassNamespace(): string;

    public function addAnnotation(Annotation $annotation);

    public function getAnnotations(): array;

    public function setImplements(array $implements);

    public function getImplements(): array;

    public function addMethodAnnotation(string $method, Annotation $model);

    public function getMethodAnnotations(): array;
}
