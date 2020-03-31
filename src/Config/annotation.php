<?php

return [
    "path" => env("ANNOTATION_CONTROLLER_PATH", "app/Http/Controllers/"),
    "namespace" => env("ANNOTATION_NAMESPACE.", "App\\Http\\Controllers"),
    "serviceIsOpen" => env("ANNOTATION_SERVICE_IS_OPEN", true),
    "servicePath" => env("ANNOTATION_SERVICE_PATH", "app/Services/"),
    "serviceNamespace" => env("ANNOTATION_SERVICE_NAMESPACE", "App\\Services")
];
