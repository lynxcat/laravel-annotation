<?php


namespace Lynxcat\Annotation;
use Illuminate\Support\Facades\Route;

class MakeCode
{
    /**
     * @var string the PHP file header template
     */
    private $headTmp = "<?php\nuse Illuminate\Support\Facades\Route;\n";

    /**
     * @var string route group template
     */
    private $groupTmp = "\n\${indentation}Route::group(\${group}, function(){\${route}\n\${indentation}});";

    /**
     * @var string route template
     */
    private $methodTmp = "\n\${indentation}Route::\${routeMethod}(\"\${path}\", \"\${controller}@\${method}\");";

    /**
     * @var string[] supported methods
     */
    private $routeMaps = ["Get", "Post", "Put", "Any", "Patch", "Delete", "Options"];

    /**
     * Get the PHP file header code
     * @return string
     */
    public function getHeadCode(): string {
        return $this->headTmp;
    }

    /**
     * Get route code
     * @param $namespace
     * @param $methods
     * @param string $indentation
     * @return string
     */
    public function getMethodCode($namespace, $methods, $indentation = ""){
        $result = "";
        foreach ($methods as $method => $value){

            foreach ($this->routeMaps as $fn){
                if(isset($value[$fn.'Mapping'])){

                    if (!empty($value[$fn."Mapping"]["value"])){
                        $path = $value[$fn."Mapping"]["value"];
                        unset($value[$fn."Mapping"]["value"]);
                    }else{
                        throw new \Exception("method annotation must have value!".$this->arrayToString($methods));
                    }

                    if (!empty($value[$fn."Mapping"])){
                        $code = $this->getGroupCode($value[$fn."Mapping"], '${indentation}');
                        $indent = $indentation."    ";
                    }else{
                        $indent = $indentation;
                        $code = '${route}';
                    }

                    $params = [$indent, strtolower($fn),$path,$namespace,$method];
                    $result .= str_replace('${route}', str_replace(['${indentation}', '${routeMethod}', '${path}', '${controller}', '${method}'], $params, $this->methodTmp), $code);
                }

            }
            return $result;
        }
    }

    /**
     * Get route group code
     * @param $annotation
     * @param string $indentation
     * @return string
     * @throws \Exception
     */
    public function getGroupCode($annotation, $indentation = ""){
        $params = [];

        if(!empty($annotation["prefix"] ?? "").($annotation["value"] ?? "")){
            $params["prefix"] = ($annotation["prefix"] ?? "").($annotation["value"] ?? "");
        }

        if (!empty($annotation['middleware'])){
            $params['middleware'] = $annotation['middleware'];
        }

        if (empty($params)){
            throw new \Exception("group annotation params has error.".$this->arrayToString($annotation));
        }

        return str_replace(['${indentation}','${group}'], [$indentation, $this->arrayToString($params)], $this->groupTmp);
    }

    /**
     * Gets the routing code within the group
     * @param $namespace
     * @param $method
     * @param $code
     * @return string
     * @throws \Exception
     */
    public function getMethodCodeInGroup($namespace, $method, $code){
        return str_replace('${route}', str_replace('${indentation}', "    ", $this->getMethodCode($namespace, $method, "    ")), $code);
    }


    /**
     * Converts an array to a string, and does not support a value type of object
     * @param array $arr
     * @return string
     */
    private function arrayToString(array $arr){

        foreach ($arr as $key => $val){
            if (is_array($val)){
                $arr[$key] = [$this->arrayToString($val)];
            }
        }

        $flag = true;
        $keys = array_keys($arr);
        for ($i = 0, $len = count($keys); $i < $len; $i++){
            if($i !== $keys[$i]){
                $flag = false;
                break;
            }
        }

        $content = "";
        foreach ($arr as $key => $val){
            if ($content != ""){
                $content .= ", ";
            }

            if ($flag){
                $content .= is_array($val) ? current($val) : (is_int($val) ? $val : '"'.$val.'"');
            }else{
                $content .= "'".$key."' => ".(is_array($val) ? current($val) : (is_int($val) ? $val : '"'.$val.'"'));
            }

        }

        return "[".$content."]";
    }

    public function getDynamicCallable(array $classes){
        return function() use ($classes) {
            foreach ($classes as $namespace => $class){
                $callable = $this->getCallable($namespace, $class["method"]);

                if (isset($class["annotation"])){

                    $params = [
                        "prefix" => ($class["annotation"]["RequestMapping"]["prefix"] ?? "").($class["annotation"]["RequestMapping"]["value"] ?? ""),
                    ];

                    isset($class["annotation"]["RequestMapping"]["middleware"]) && $params["middleware"] = $class["annotation"]["RequestMapping"]["middleware"];

                   Route::group($params, $callable);
                }else{
                    call_user_func($callable);
                }
            }
        };
    }

    public function getCallable($namespace, $methods){
        return function() use ($namespace, $methods){
            foreach ($methods as $method => $value){
                foreach ($this->routeMaps as $fn){
                    if(isset($value[$fn.'Mapping'])){
                        $path = $value[$fn."Mapping"]["value"];
                        unset($value[$fn."Mapping"]["value"]);
                        Route::group($value[$fn."Mapping"], function () use($path, $namespace, $method, $fn) {
                            $fn = strtolower($fn);
                            Route::$fn($path, $namespace."@".$method);
                        });
                    }
                }
            }
        };
    }
}
