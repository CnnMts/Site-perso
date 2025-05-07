<?php 

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\AuthModel;
use App\Utils\{Route, HttpException};
use App\Middlewares\{AuthMiddleware,Roles,RoleMiddleware};

class Auth extends Controller {
  protected object $auth;

  public function __construct($params) {
    $this->auth = new AuthModel();
    parent::__construct($params);
  }

 /*========================= REGISTER =======================================*/

  #[Route("POST", "/auth/register",
  /*middlewares: [AuthMiddleware::class, 
  [RoleMiddleware::class, Roles::ROLE_ADMIN]]*/)]
  public function register() {
      try {
          $data = $this->body;
          $user = $this->auth->register($data);
          return $user;
      } catch (\Exception $e) {
          throw new HttpException($e->getMessage(), 400);
      }
  }

 /*========================= LOGIN ==========================================*/

  #[Route("POST", "/auth/login")]
  public function login() {
    try {
        $data = $this->body;

        if (empty($data['email']) || empty($data['password'])) {
            throw new HttpException("Email ou mot de passe manquant.", 400);
        }

        $token = $this->auth->login($data['email'], $data['password']);
        return $token;

    } catch (\Exception $e) {
        throw new HttpException($e->getMessage(), 401);
    }
}


}