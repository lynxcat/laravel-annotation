<?php


namespace Lynxcat\Annotation\Contracts\Service;

use Lynxcat\Annotation\Contracts\Model\Files;

interface ReaderFactory
{
    public function setFiles(Files $files): ReaderFactory;

    public function setReaderClasses(array $classes): ReaderFactory;

    public function getAnnotationsClass(): array;

    public function start(): ReaderFactory;

}
