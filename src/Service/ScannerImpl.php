<?php

namespace Lynxcat\Annotation\Service;

use Lynxcat\Annotation\Contracts\Model\Files;
use Lynxcat\Annotation\Contracts\Service\Scanner;
use Lynxcat\Annotation\Model\FilesModel;

class ScannerImpl implements Scanner
{
    private $files;
    private $path;
    private $namespace;

    private $isScan = false;

    public function __construct()
    {
        $this->files = new FilesModel();
    }

    public function getFiles(): Files
    {
        if (!$this->isScan) {
            $this->scanFiles();
        }

        return $this->files;
    }

    /**
     * scan controller
     * @param string $path
     */
    public function scanFiles($path = ""): void
    {
        $dirHandle = opendir($this->path . $path);

        while ($file = readdir($dirHandle)) {
            if ($file !== "." && $file != "..") {
                if (is_dir($this->path . $path . $file)) {
                    $this->scanFiles($path . $file . DIRECTORY_SEPARATOR);
                } else {
                    $this->files->push($this->path . $path . $file, $this->namespace . "\\" . str_replace(DIRECTORY_SEPARATOR, "\\", $path) . str_replace(".php", "", $file));
                }
            }
        }

        $this->isScan = true;
    }

    public function setPathAndNamespace(string $path, string $namespace): Scanner
    {
        $this->path = $path;
        $this->namespace = $namespace;
        $this->isScan = false;
        return $this;
    }

    public function scan(): Scanner
    {
        if (!$this->isScan) {
            $this->scanFiles();
        }
        return $this;
    }
}
