<?php


namespace Lynxcat\Annotation\Service;

use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;
use Lynxcat\Annotation\Contracts\Service\Reader;
use Lynxcat\Annotation\Model\AnnotationModel;
use Lynxcat\Annotation\Model\AnnotationsClassModel;

class RouteReaderImpl implements Reader
{
    private $regx = "/@(\w+)(\(.*\)){1}/";

    private $model;

    public function __construct()
    {
        $this->model = new AnnotationsClassModel();
        $this->model->setType($this->getType());
    }

    public function getType(): string
    {
        return "route";
    }

    public function parse(\ReflectionClass $ref): AnnotationsClass
    {
        $this->model->setClassName($ref->getName());
        $this->model->setClassNamespace($ref->getNamespaceName());
        $this->parseClassAnnotations($ref->getDocComment());
        $this->parseMethodsAnnotations($ref->getMethods());
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
     * parse methods annotations
     * @param array $methods
     */
    private function parseMethodsAnnotations(array $methods)
    {
        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            $annotations = $this->parseAnnotations($docComment);
            foreach ($annotations as $annotation) {
                $this->model->addMethodAnnotation($method->getName(), $annotation);
            }
        }
    }

    /**
     * parse annotation comment
     * @param string $docComment
     * @return array
     */
    private function parseAnnotations(string $docComment): array
    {
        $result = [];
        preg_match_all($this->regx, $docComment, $result);
        $annotations = [];
        if (!empty($result[1])) {
            for ($i = 0, $len = count($result[1]); $i < $len; $i++) {
                $annotation = new AnnotationModel();
                $annotation->setName($result[1][$i]);
                $annotation->setParams($this->parseAnnotationParams($result[2][$i]));
                array_push($annotations, $annotation);
            }
        }
        return $annotations;
    }

    /**
     * parse annotation params
     * @param $params
     * @return array
     */
    public function parseAnnotationParams($params): array
    {
        $params = trim($params, " \t\n\r \v()");
        $result = [];
        preg_match_all("/(\w+)=((\".*\")|(\{.*\}))/U", $params, $result);
        $len = count($result[0]);
        $res = [];
        if ($len == 0) {
            $res["value"] = trim($params, "\"");
        } else {
            for ($i = 0; $i < $len; $i++) {
                $res[$result[1][$i]] = $this->parseParamValue($result[2][$i]);
            }
        }

        return $res;
    }

    /**
     * parse annotation param value
     * @param $value
     * @return string|array
     */
    public function parseParamValue($value)
    {
        if ($value[0] == "\"") {
            $result = trim($value, "\"");
        } else {
            $result = explode(", ", str_replace(["\"", "{", "}"], "", $value));
        }
        return $result;
    }
}
