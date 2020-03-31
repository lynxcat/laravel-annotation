<?php


namespace Lynxcat\Annotation\Service;


class Cache
{
    /**
     * @var string route file
     */
    public static $routeFile = __DIR__ . "/../Cache/route_cache.php";


    /**
     * @var string route file
     */
    public static $serviceFile = __DIR__ . "/../Cache/service_cache.php";


    /**
     * @var string head code template
     */
    private $routeHeadCodeTmp = "<?php\nuse Illuminate\Support\Facades\Route;";

    /**
     * @var string head codetmplate
     */
    private $serviceCodeTmp = "<?php\n";

    /**
     * route is cached
     *
     * @return bool
     */
    public static function isRouteCache(): bool
    {
        return is_file(self::$routeFile);
    }

    /**
     * service is cached
     *
     * @return bool
     */
    public static function isServiceCache(): bool
    {
        return is_file(self::$serviceFile);
    }

    /**
     * load route file
     */
    public static function loadRouteCache(): string
    {
        return self::$routeFile;
    }

    /**
     * load service file
     */
    public static function loadServiceCache(): string
    {
        return self::$serviceFile;
    }

    /**
     * remove route file
     */
    public static function clearCache(): void
    {
        if (self::isRouteCache()) {
            unlink(self::$routeFile);
        }

        if (self::isServiceCache()) {
            unlink(self::$serviceFile);
        }
    }

    /**
     * @param string $code
     */
    public function routeCache(string $code): void
    {
        file_put_contents(self::$routeFile, $this->routeHeadCodeTmp);
        file_put_contents(self::$routeFile, $code, FILE_APPEND);
    }

    /**
     * @param string $code
     */
    public function serviceCache(string $code): void
    {
        file_put_contents(self::$serviceFile, $this->serviceCodeTmp);
        file_put_contents(self::$serviceFile, $code, FILE_APPEND);
    }
}
