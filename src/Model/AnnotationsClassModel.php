<?php
namespace Lynxcat\Annotation\Model;
use Lynxcat\Annotation\Contracts\Model\Annotation;
use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;

class AnnotationsClassModel implements AnnotationsClass {

    private $type;

    private $className;

    private $classNamespace;

    private $annotations = [];

    private $implements = [];

    private $methodsModel = [];

    public function setType(string $type){
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setClassName(string $name){
        $this->className = $name;
    }

    public function getClassName(): string {
        return $this->className;
    }

    public function setClassNamespace(string $namespace){
        $this->classNamespace = $namespace;
    }

    public function getClassNamespace(): string {
        return $this->classNamespace;
    }

    public function getAnnotations(): array {
        return $this->annotations;
    }

    public function addAnnotation(Annotation $annotation){
        array_push($this->annotations, $annotation);
    }

    public function setImplements(array $implements)
    {
        $this->implements = $implements;
    }

    public function getImplements(): array
    {
        return $this->implements;
    }

    public function addMethodAnnotation(string $method, Annotation $model){
        array_push($this->methodsModel, [$method, $model]);
    }

    public function getMethodAnnotations(): array {
        return $this->methodsModel;
    }
}
