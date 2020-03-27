<?php
namespace Lynxcat\Annotation;

class MakeRoute
{
    /**
     * @var string route file
     */
    public static $file = __DIR__."/route_cache.php";

    /**
     * @var array all class
     */
    private $classes;

    /**
     * @var MakeCode make route code
     */
    private $makeCode;

    public function __construct(array $classes)
    {
        $this->classes = $classes;
        $this->makeCode = new MakeCode();
    }

    /**
     * is cached
     *
     * @return bool
     */
    public static function isCache(){
        return is_file(self::$file);
    }

    /**
     * load route file
     */
    public static function loadCache(){
        return self::$file;
    }

    /**
     * remove route file
     */
    public static function clearCache(){
        unlink(self::$file);
    }

    /**
     * create route file
     * @throws \Exception
     */
    public function cache(){
        file_put_contents(self::$file, $this->makeCode->getHeadCode());

        foreach ($this->classes as $namespace => $class){
            if (!empty($class["annotation"])){
                $code =  $this->makeCode->getGroupCode($class['annotation']['RequestMapping']);
                $code = $this->makeCode->getMethodCodeInGroup($namespace, $class["method"], $code);
            }else{
                $code = $this->makeCode->getMethodCode($namespace, $class['method']);
            }

            file_put_contents(self::$file, $code, FILE_APPEND);
        }
    }

    /**
     * dynamic route
     */
    public function dynamicAddRoute(){
        call_user_func($this->makeCode->getDynamicCallable($this->classes));
    }
}
