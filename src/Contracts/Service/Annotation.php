<?php
namespace Lynxcat\Annotation\Contracts\Service;

use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;

interface Annotation
{
    public function setModel(AnnotationsClass $model);

    public function getCallable(): callable;

    public function getCode(): string;

    public function getType(): string;
}
