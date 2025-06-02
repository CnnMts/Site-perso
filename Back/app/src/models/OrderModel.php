<?php

namespace App\Models;

use \PDO;
use stdClass;

class OrderModel extends SqlConnect {
  private $table = "orders";
  public $authorized_fields_to_update = [
    'user_id', 'status_id', 'discount_id', 'payment_method_id'];

  /*========================= ADD ===========================================*/

  public function add(array $data) {
    $statusTest = 1;
    $paymentMethodPaypal = 1;
    $checkQuery = "
        SELECT COUNT(*)
        FROM $this->table
        WHERE user_id = :user_id AND status_id = :status_id
    ";

    $checkReq = $this->db->prepare($checkQuery);
    $checkReq->execute([
        'user_id' => $data['user_id'],
        'status_id' => $data['status_id']
    ]);

    $orderExists = (bool) $checkReq->fetchColumn();

    if ($orderExists) {
        if ($data['status_id'] === $statusTest && $data['payment_method_id'] === $paymentMethodPaypal) {
            $query = "
              UPDATE $this->table
              SET total_price = :total_price,
                  picture = :picture
              WHERE user_id = :user_id AND status_id = :status_id AND payment_method_id = :payment_method_id
            ";
        }
    } else {
        $query = "
          INSERT INTO $this->table (user_id, status_id, total_price, payment_method_id, picture)
          VALUES (:user_id, :status_id, :total_price, :payment_method_id, :picture)
        ";
    }

    $req = $this->db->prepare($query);
    $req->execute($data);
}



  /*========================= GET ===========================================*/

  public function get(int $id) {
    $req = $this->db->prepare("SELECT * FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);

    return $req->rowCount() > 0 ? 
      $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }

  /*========================= GET ALL =======================================*/

  public function getAll(?int $limit = null) {
    $query = "SELECT * FROM {$this->table}";
    
    if ($limit !== null) {
      $query .= " LIMIT :limit";
      $params = [':limit' => (int)$limit];
    } else {
      $params = [];
    }
    
    $req = $this->db->prepare($query);
    foreach ($params as $key => $value) {
      $req->bindValue($key, $value, PDO::PARAM_INT);
    }
    $req->execute();
    
    return $req->fetchAll(PDO::FETCH_ASSOC);
  }



  /*======================== GET WITH ITEMS =================================*/
  public function getAllOrdersWithItems() {
      $query = "SELECT 
                o.*, 
                oi.product_id, 
                p.name AS product_name
              FROM orders o
              LEFT JOIN order_items oi ON o.id = oi.order_id
              LEFT JOIN product p ON oi.product_id = p.id
              ORDER BY o.updated_at DESC";

      $req = $this->db->prepare($query);
      $req->execute();
      $results = $req->fetchAll(PDO::FETCH_ASSOC);
      $orders = [];
      foreach ($results as $row) {
        $orderId = $row['id'];

          if (!isset($orders[$orderId])) {
              $orders[$orderId] = [
                  'id' => $row['id'],
                  'user_id' => $row['user_id'],
                  'total_price' => $row['total_price'],
                  'updated_at' => $row['updated_at'],
                  'picture' => $row['picture'],
                  'items' => []
              ];
          }
          if ($row['product_id']) {
              $orders[$orderId]['items'][] = [
                  'product_id' => $row['product_id'],
                  'name' => $row['product_name'],
              ];
          }
      }
    return array_values($orders);
  }





  /*========================= GET LAST ======================================*/

  public function getLast() {
    $req = $this->db->prepare(
      "SELECT * FROM $this->table ORDER BY id DESC LIMIT 1");
    $req->execute();

    return $req->rowCount() > 0 ? 
      $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }

  /*========================= UPDATE ========================================*/

  public function update(array $data, int $id) {
    $request = "UPDATE $this->table SET ";
    $params = [];
    $fields = [];

    foreach ($data as $key => $value) {
      if (in_array($key, $this->authorized_fields_to_update)) {
        $fields[] = "$key = :$key";
        $params[":$key"] = $value;
      }
    }

    $params[':id'] = $id;
    $query = $request . implode(", ", $fields) . " WHERE id = :id";

    $req = $this->db->prepare($query);
    $req->execute($params);
    
    return $this->get($id);
  }

  /*========================= TOGGLE ========================================*/
  public function toggleOrderStatus(int $orderId) {
    // Récupérer le statut actuel
    $stmt = $this->db->prepare("SELECT status_id FROM `orders` WHERE id = :orderId");
    $stmt->execute(['orderId' => $orderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        throw new Exception("Commande non trouvée");
    }

    $currentStatus = intval($row['status_id']);
    // Définir le nouveau statut : s'il est 1 (await_payment), on le passe à 2 (paid), sinon à 1.
    $newStatus = ($currentStatus === 1) ? 2 : 1;

    $stmt = $this->db->prepare("UPDATE `orders` SET status_id = :newStatus WHERE id = :orderId");
    $stmt->execute(['newStatus' => $newStatus, 'orderId' => $orderId]);

    return $newStatus;
  }


  /*========================= DELETE ========================================*/

  public function delete(int $id) {
    $req = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);
    return new stdClass();
  }
}