<?php


namespace Lynxcat\Annotation\Contracts\Service;

use Lynxcat\Annotation\Contracts\Model\Files;


interface Scanner
{
    public function setPathAndNamespace(string $file, string $namespace): Scanner;

    public function getFiles(): Files;
}
