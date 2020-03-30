<?php

namespace Lynxcat\Annotation\Contracts\Model;

interface Files
{
    public function push(string $file, string $class);

    public function getFiles(): array;
}
