<?php
namespace JoelAlejandroDescanse;

class Descanse {

  public static $services;

  public static $settings = array(
    "auto_register" => true
  );

  private static function failRequest() {
    header("HTTP/1.1 400 Bad Request");
    exit;
  }

  private static function attendRequest() {
    $uri = explode("/", substr($_SERVER["REQUEST_URI"], 1));
    $http_method = $_SERVER["REQUEST_METHOD"];
    $format = strpos($_SERVER["HTTP_ACCEPT"], ",") 
      ? explode(",", $_SERVER["HTTP_ACCEPT"])[0]
      : $_SERVER["HTTP_ACCEPT"];

    $service = self::getServiceBySlug($uri[0]);
    $method = self::getMethodBySlug($service["slug"], $uri[1]);

    $data = $service["name"]::{$method["name"]}(array(
      "request" => $_REQUEST, 
      "args" => array_slice($uri, 2)
    ));

    switch ($format) {
      case "application/json":
        $response = json_encode($data);
      break;
      case "text/plain":
        $response = $data;
      break;
    }

    header("Content-Type: $format");
    header("Content-Length: " . strlen($response));
    echo $response;
  }

  private static function getServiceBySlug($slug) {
    $services = array_filter(self::$services, function($s) use ($slug) { return $s["slug"] == $slug; });
    if (count($services) > 0) {
      return $services[$slug];
    } else {
      return null;
    }
  }

  private static function getMethodBySlug($service_slug, $method_slug) {
    if (isset(self::$services[$service_slug])) {
      if (isset(self::$services[$service_slug]["methods"][$method_slug])) {
        return self::$services[$service_slug]["methods"][$method_slug];
      } else {
        self::failRequest();
      }
    } else {
      self::failRequest();
    }
  }

  public static function go() {
    if (self::$settings["auto_register"]) {
      foreach (array_filter(get_declared_classes(), function($c) {
        return strrpos($c, "Service") !== false;
      }) as $service) {
        self::registerSingleService($service);
      }
    }

    self::attendRequest();
  }

  private static function getServiceMethods($class_name) {
    return (new ReflectionClass($class_name))->getMethods(ReflectionMethod::IS_PUBLIC);
  }

  private static function registerSingleService($name) {
    $newService = array();
    if (strrpos($name, "Service") !== false) {
       $newService["name"] = $name;
    } else {
      if (in_array($name . "Service", get_declared_classes())) {
        $newService["name"] = $name . "Service";
      }
    }

    $newService["slug"] = strtolower(str_replace("Service", "", $newService["name"]));

    foreach (self::getServiceMethods($newService["name"]) as $method) {
      foreach (array("get", "post", "put", "delete") as $hm) {
        if (stripos($method->name, $hm) === 0) {
          $newService["methods"][strtolower(str_replace($hm, "", $method->name))] = array(
            "name" => $method->name,
            "slug" => strtolower(str_replace($hm, "", $method->name)),
            "http_method" => $hm
          );
        }
      }
    }

    self::$services[$newService["slug"]] = $newService;
  }

  public static function registerService($service_name) {
    if (is_array($service_name)) {
      foreach ($service_name as $sn) {
        self::registerSingleService($sn);
      }
    } else {
      self::registerSingleService($service_name);
    }
  }

}
