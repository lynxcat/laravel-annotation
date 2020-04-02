<?php


namespace Lynxcat\Annotation\Service;


use Lynxcat\Annotation\Contracts\Model\AnnotationsClass;
use Lynxcat\Annotation\Contracts\Service\Annotation;
use Lynxcat\Annotation\Util\Util;
use Illuminate\Support\Facades\Route;

class RouteAnnotationImpl implements Annotation
{
    /**
     * @var string route group template
     */
    private $groupTmp = "\n\${indentation}Route::group(\${group}, function(){\${code}\n\${indentation}});";

    /**
     * @var string route template
     */
    private $methodTmp = "\n\${indentation}Route::\${routeMethod}(\"\${path}\", \"\${controller}@\${method}\");";

    /**
     * @var string[] supported methods
     */
    private $routeMaps = ["Get", "Post", "Put", "Any", "Patch", "Delete", "Options"];

    private $indentation = "    ";

    private $repeat = 0;

    private $model;

    public function setModel(AnnotationsClass $model)
    {
        if ($model->getType() == $this->getType()) {
            $this->model = $model;
            $this->repeat = 0;
        } else {
            throw new \Exception("requires an annotations class type of " . $this->getType());
        }
    }

    public function getType(): string
    {
        return 'route';
    }

    public function getCallable(): callable
    {
        $model = $this->model;
        $callable = $this->getReduceCallable();
        $fn = function () use ($model, $callable) {
            $annotations = $model->getAnnotations();

            return array_reduce($annotations, $callable, function () use ($model) {
                $annotations = $model->getMethodAnnotations();
                $class = $model->getClassName();

                foreach ($annotations as $annotation) {
                    $method = $annotation[0];

                    $annotation = $annotation[1];

                    $fn = str_replace("Mapping", "", $annotation->getName());

                    if (in_array($fn, $this->routeMaps)) {
                        $fn = strtolower($fn);
                        $params = $annotation->getParams();
                        $path = $params["value"];
                        unset($params["value"]);
                        $method = $class . "@" . $method;

                        Route::group($params, function () use ($fn, $path, $method) {
                            Route::$fn($path, $method);
                        });
                    }
                }
            });
        };

        return $fn();
    }

    private function getReduceCallable()
    {
        return function ($carry, $item) {
            return function () use ($carry, $item) {
                $params = $item->getParams();
                $params["prefix"] = ($params["prefix"] ?? "") . ($params["value"] ?? "");
                unset($params["value"]);
                Route::group($params, $carry);
            };
        };
    }

    /**
     * @return string
     * @throws \Exception
     */

    public function getCode(): string
    {
        $code = str_replace('${class}', $this->model->getClassName(), "\n\n/**\n * @class \${class}\n */\${code}");

        $annotations = $this->model->getAnnotations();
        foreach ($annotations as $key => $annotation) {
            $name = $annotation->getName();
            $params = $annotation->getParams();
            if ($name == "RequestMapping" && !empty($params)) {
                $code = str_replace('${code}', $this->getGroupCode($params, str_repeat($this->indentation, $this->repeat++)), $code);
            }
        }

        $code = str_replace('${code}', $this->getMethodCode($this->model->getClassName(), $this->model->getMethodAnnotations(), str_repeat($this->indentation, $this->repeat)), $code);
        return $code;
    }

    /**
     * Get route code
     * @param $class
     * @param $methods
     * @param string $indentation
     * @return string
     * @throws \Exception
     */
    public function getMethodCode($class, $methods, $indentation = ""): string
    {
        $result = "";
        foreach ($methods as $value) {

            $method = $value[0];
            $annotation = $value[1];

            $name = str_replace("Mapping", "", $annotation->getName());
            $params = $annotation->getParams();

            if (in_array($name, $this->routeMaps)) {
                if (!empty($params['value'])) {
                    $path = $params['value'];
                    unset($params['value']);
                } else {
                    throw new \Exception("method annotation must have value!" . Util::arrayToString($methods));
                }

                if (!empty($params)) {
                    $code = $this->getGroupCode($params, $indentation);
                    $indent = $indentation . "    ";
                } else {
                    $indent = $indentation;
                    $code = '${code}';
                }

                $params = [$indent, strtolower($name), $path, $class, $method];
                $result .= str_replace('${code}', str_replace(['${indentation}', '${routeMethod}', '${path}', '${controller}', '${method}'], $params, $this->methodTmp), $code);
            }
        }
        return $result;
    }

    /**
     * Get route group code
     * @param $annotation
     * @param string $indentation
     * @return string
     * @throws \Exception
     */
    public function getGroupCode($annotation, $indentation = "")
    {
        $params = [];

        if (!empty(($annotation["prefix"] ?? "") . ($annotation["value"] ?? ""))) {
            $params["prefix"] = ($annotation["prefix"] ?? "") . ($annotation["value"] ?? "");
        }

        if (!empty($annotation['middleware'])) {
            $params['middleware'] = $annotation['middleware'];
        }

        if (empty($params)) {
            throw new \Exception("group annotation params has error." . Util::arrayToString($annotation));
        }

        return str_replace(['${indentation}', '${group}'], [$indentation, Util::arrayToString($params)], $this->groupTmp);
    }
}
