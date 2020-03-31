<?php


namespace Lynxcat\Annotation\Service;


use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;
use Lynxcat\Annotation\Model\AnnotationModel;
use Lynxcat\Annotation\Model\AnnotationsClassModel;
use Lynxcat\Annotation\Contracts\Service\Reader;

class ServiceReaderImpl implements Reader
{
    private $regx = "/@(Service)(\(.*\))?/u";

    private $model;

    public function __construct()
    {
        $this->model = new AnnotationsClassModel();
        $this->model->setType($this->getType());
    }

    public function getType(): string
    {
        return 'service';
    }

    public function parse(\ReflectionClass $ref): AnnotationsClass
    {
        $this->model->setClassName($ref->getName());
        $this->model->setClassNamespace($ref->getNamespaceName());
        $this->model->setImplements($ref->getInterfaceNames());
        $this->parseClassAnnotations($ref->getDocComment());
        return $this->model;
    }

    /**
     * parse class annotations
     * @param string $docComment
     */
    private function parseClassAnnotations(string $docComment)
    {
        $annotations = $this->parseAnnotations($docComment);
        foreach ($annotations as $annotation) {
            $this->model->addAnnotation($annotation);
        }
    }

    /**
     * parse annotation comment
     * @param string $docComment
     */
    private function parseAnnotations(string $docComment)
    {
        $result = [];
        preg_match_all($this->regx, $docComment, $result);
        $annotations = [];

        if (!empty($result[1])) {
            for ($i = 0, $len = count($result[1]); $i < $len; $i++) {
                $annotation = new AnnotationModel();
                $annotation->setName($result[1][$i]);
                array_push($annotations, $annotation);
            }
        }
        return $annotations;
    }
}
