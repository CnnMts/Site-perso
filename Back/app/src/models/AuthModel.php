<?php

namespace App\Models;

use App\Models\SqlConnect;
use App\Utils\{HttpException, JWT};
use App\Middlewares\Roles;
use \PDO;
use \Exception;



class AuthModel extends SqlConnect {
  private string $table  = "user";
  private int $tokenValidity = 3600*24;
  private string $tableRole = "role";
  
  /*========================= REGISTER ======================================*/

    public function register(array $data) {
    
      $query = "SELECT email FROM $this->table WHERE email = :email";
      $req = $this->db->prepare($query);
      $req->execute(["email" => $data["email"]]);
    
 
      $queryRole = "SELECT id FROM $this->tableRole WHERE name = :name";
      $reqRole = $this->db->prepare($queryRole);
      $reqRole->execute(['name' => 'admin']);
      $role = $reqRole->fetch(PDO::FETCH_ASSOC);
      $roleId = $role['id'];
  
    
      if (!isset($data["password"]) || strlen($data["password"]) <= 5) {
          throw new Exception('Password must be at least 6 characters long.');
      }
  
      if (!preg_match('/[0-9]/', $data["password"])) {
          throw new Exception('Password must include at least one number.');
      }
  
      
      $hashedPassword = password_hash($data["password"], PASSWORD_BCRYPT);
  
     
      $requiredFields = ['name', 'email', 'password', 'firstname', 'address', 'zip_code', 'city'];
      foreach ($requiredFields as $field) {
          if (empty($data[$field])) {
              throw new Exception("Missing field: $field");
          }
      }
      $queryAdd = "
          INSERT INTO $this->table (
              name, email, password, firstname, address, zip_code, city
          ) VALUES (
               :name, :email, :password, :firstname, :address, :zip_code, :city
          )
      ";
  
      $req2 = $this->db->prepare($queryAdd);
      $req2->execute([
          "name"      => $data['name'],
          "email"     => $data['email'],
          "password"  => $hashedPassword,
          "firstname" => $data['firstname'],
          "address"   => $data['address'],
          "zip_code"  => $data['zip_code'],
          "city"  => $data['city'],
      ]);
  
      $userId = $this->db->lastInsertId();
  
      $token = $this->generateJWT($userId, $roleId);
  
      return ['token' => $token];
  }
  

  /*========================= LOGIN =========================================*/
  public function login($email, $password) {
    $query = "SELECT user.*, role.name 
              FROM $this->table 
              JOIN role ON user.role_id = role.id 
              WHERE user.email = :email";
    
    $req = $this->db->prepare($query);
    $req->execute(['email' => $email]);

    $user = $req->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $token = $this->generateJWT($user['id'], $user['role_id']);
            return [
                    'token' => $token,
                    ];

        }
        throw new HttpException("Mauvais mot de passe", 401);
    }

    throw new HttpException("Utilisateur non trouvé", 404);
}



/*============================LOGOUT=========================================*/
    public function logout(): array {
        if (isset($_COOKIE['jwt'])) {
            setcookie('jwt', '', time() - 3600, '/', '', false, true); 
            unset($_COOKIE['jwt']);
        }

        return ["message" => "Déconnexion réussie"];
}



  /*========================= JWT  ==========================================*/

  private function generateJWT(int $userId, int $role) {
    JWT::initialize();

    $payload = [
        'id' => $userId,
        'role' => $role,
        'exp' => time() + $this->tokenValidity
    ];

    return JWT::generate($payload);
}

}