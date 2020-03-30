<?php


namespace Lynxcat\Annotation\Service;


class RouteCache
{
    /**
     * @var string route file
     */
    public static $file = __DIR__."/../Cache/route_cache.php";

    /**
     * @var string head codetmplate
     */
    private $headCodeTmp = "<?php\nuse Illuminate\Support\Facades\Route;";

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
     * @param string $code
     */
    public function cache(string $code){
        file_put_contents(self::$file, $this->headCodeTmp);
        file_put_contents(self::$file, $code, FILE_APPEND);
    }
}
