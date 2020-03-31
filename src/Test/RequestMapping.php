<?php

namespace Lynxcat\Annotation\Test;

use Lynxcat\Annotation\Contracts\Model\Files;

/**
 * Class RequestMapping
 * @package Lynxcat\Annotation\Test
 * @RequestMapping("manage")
 * @RequestMapping("xxxxx")
 * @Servie
 */
class RequestMapping implements Files
{
    /**
     * @GetMapping("1")
     * @PostMapping(value="2", middleware={"auth"})
     */
    public function getName()
    {

    }

    /**
     * @param string $file
     * @GetMapping("/{file}")
     * @PostMapping(value="/create/{file}", prefix="manage")
     */
    public function push(string $file, string $class)
    {
        // TODO: Implement push() method.
    }

    public function getFiles(): array
    {
        return [];
    }
}
