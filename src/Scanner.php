<?php


namespace Lynxcat\Annotation;


class Scanner
{
    /**
     * @var string controller path
     */
    private $path;

    /**
     * @var string namespace
     */
    private $namespace;

    /**
     * @var array all controller class
     */
    private $controllers = [];

    public function __construct($path, $namespace)
    {
        $this->path = $path;
        $this->namespace = $namespace;
    }

    /**
     * scan controller
     * @param string $path
     */
    public function scanAllControllerDir($path = ""){
        $dirHandle = opendir($this->path.$path);

        while($file = readdir($dirHandle)){
            if($file !== "." && $file != ".."){
                if(is_dir($this->path.$path.$file)){
                    $this->scanAllControllerDir($path.$file.DIRECTORY_SEPARATOR);
                }else{
                    array_push($this->controllers, $this->namespace."\\". str_replace(DIRECTORY_SEPARATOR, "\\", $path).str_replace(".php", "", $file));
                }
            }
        }
    }

    /**
     * get all controller
     * @return array
     */
    public function getControllers(){
        return $this->controllers;
    }
}
