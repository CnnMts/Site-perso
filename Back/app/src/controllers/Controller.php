<?php

namespace App\Controllers;

class Controller {
  protected array $params;
  protected string $reqMethod;
  protected array $body;
  protected string $className;

  public function __construct($params) {
    $this->className = $this->getCallerClassName();
    $this->params = $params;
    $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
    $this->body = (array) json_decode(file_get_contents('php://input'));

    $this->header();
  }

 /*========================= GET CALLER CLASS NAME ==========================*/

  protected function getCallerClassName() {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

    if (isset($backtrace[1]['object'])) {
      $fullClassName = get_class($backtrace[1]['object']);
      $className = basename(str_replace('\\', '/', $fullClassName));

      return $className;
    }

    return 'Unknown';
  }

   /*========================= HEADER =======================================*/

 protected function header() {
    $allowedOrigins = ['http://localhost:5173'];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    header('Content-type: application/json; charset=utf-8');
}

}