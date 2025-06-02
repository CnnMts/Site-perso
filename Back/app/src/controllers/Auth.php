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
            $user = $this->auth->login($data['email'], $data['password']);
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = $user;
            $options = [
                'expires'  => time() + 10000,
                'path'     => '/',
                'secure'   => false,   
                'httponly' => false,
                'samesite' => 'Lax'
            ];
            setcookie("pmaUser", $user['token'], $options);
        return $user;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), 401);
        }
    }

 /*========================= LOGOUT =========================================*/

    #[Route("POST", "/auth/logout")]
    public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_unset();
    session_destroy();
    setcookie("pmaUser", "", [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => false,
        'httponly' => false,
        'samesite' => 'Lax'
    ]);

    return ['message' => 'Déconnexion réussie'];
}


}