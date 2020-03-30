<?php


namespace Lynxcat\Annotation\Model;

use Lynxcat\Annotation\Contracts\Model\Annotation;

class AnnotationModel implements Annotation
{
    private $name;

    private $params;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
