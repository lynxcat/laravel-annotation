<?php

namespace Lynxcat\Annotation\Model;

use Lynxcat\Annotation\Contracts\Model\Files;

class FilesModel implements Files
{
    private $files = [];

    public function push(string $file, string $class)
    {
        $this->files[$file] = $class;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
