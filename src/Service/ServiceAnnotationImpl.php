<?php


namespace Lynxcat\Annotation\Service;


use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;
use Lynxcat\Annotation\Contracts\Service\Annotation;

class ServiceAnnotationImpl implements Annotation
{
    private $model;

    private $bindCodeTmp = "\n\$this->app->bind(\"\${implement}\", \"\${class}\");";

    public function setModel(AnnotationsClass $model)
    {
        if ($model->getType() == $this->getType()) {
            $this->model = $model;
        } else {
            throw new \Exception("requires an annotations class type of " . $this->getType());
        }
    }

    public function getCallable(): callable
    {
        $class = $this->model->getClassName();
        $implements = $this->model->getImplements();

        return function ($app) use ($implements, $class) {
            foreach ($implements as $implement) {
                $app->bind($implement, $class);
            }
        };
    }

    public function getCode(): string
    {
        $class = $this->model->getClassName();
        $implements = $this->model->getImplements();

        $code = "";
        foreach ($implements as $implement) {
            $code .= str_replace(['${implement}', '${class}'], [$implement, $class], $this->bindCodeTmp);
        }
        return $code;
    }

    public function getType(): string
    {
        return "service";
    }
}
