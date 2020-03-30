<?php


namespace Lynxcat\Annotation\Contracts\Model;


interface Annotation
{
    public function setName(string $name);

    public function getName(): string;

    public function setParams(array $params);

    public function getParams(): array;

}
