<?php
namespace Lynxcat\Annotation\Model;

use Lynxcat\Annotation\Contracts\Model\Files;

class FilesModel implements Files
{
    /**
     * @var array files
     */
    private $files = [];

    /**
     * push file
     * @param $file
     */
    public function push(string $file, string $class){
        $this->files[$file] = $class;
    }

    /**
     * get all file
     * @return array
     */
    public function getFiles(): array {
        return $this->files;
    }
}
