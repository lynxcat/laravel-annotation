<?php


namespace Lynxcat\Annotation\Contracts\Service;


use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;

interface Reader
{
    public function parse(\ReflectionClass $ref): AnnotationsClass;

    public function getType(): string;
}
