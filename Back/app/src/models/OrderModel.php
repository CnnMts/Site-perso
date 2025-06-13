<?php

namespace App\Models;

use \PDO;
use stdClass;

class OrderModel extends SqlConnect {
  private $table = "orders";
  public $authorized_fields_to_update = [
    'status_id', 'discount_id', 'payment_method_id'];

  /*========================= ADD ===========================================*/
  public function add(array $data) {
    try {
        $sqlCheck = "SELECT id, status_id FROM orders WHERE user_id = :user_id AND status_id != 3 ORDER BY id DESC LIMIT 1";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([':user_id' => $data['user_id']]);
        $existingOrder = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($existingOrder !== false && !empty($existingOrder)) {
            $orderId = $existingOrder['id'];
            $sqlUpdate = "UPDATE orders SET total_price = total_price + :total_price WHERE id = :id";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':total_price' => $data['total_price'],
                ':id' => $orderId
            ]);
        } else {
            $sqlInsert = "INSERT INTO orders (user_id, status_id, total_price, payment_method_id) VALUES (:user_id, :status_id, :total_price, :payment_method_id)";
            $stmtInsert = $this->db->prepare($sqlInsert);
            $stmtInsert->execute([
                ':user_id' => $data['user_id'],
                ':status_id' => $data['status_id'],
                ':total_price' => $data['total_price'],
                ':payment_method_id' => $data['payment_method_id'],
            ]);
            $orderId = $this->db->lastInsertId();
        }

        return ['id' => $orderId];

    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
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


  /*===================== GET ALL ORDER BY USER ID ===============================*/
  public function getAllOrdersByUserId($userId)
{
    $sql = "
        SELECT 
            orders.id,
            orders.total_price,
            orders.status_id,
            orders.updated_at,
            order_items.id AS item_id,
            product.name
        FROM orders
        LEFT JOIN order_items ON orders.id = order_items.order_id
        LEFT JOIN product ON order_items.product_id = product.id
        WHERE orders.user_id = :user_id
        ORDER BY orders.updated_at DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $orders = [];

    foreach ($results as $row) {
        $orderId = $row['id']; // orders.id
        if (!isset($orders[$orderId])) {
            $orders[$orderId] = [
                'order_id' => $orderId,
                'total_price' => $row['total_price'],
                'status_id' => $row['status_id'],
                'updated_at' => $row['updated_at'],
                'items' => []
            ];
        }

        if ($row['item_id']) {
            $orders[$orderId]['items'][] = [
                'item_id' => $row['item_id'],
                'product_name' => $row['name'], // product.name
                'sale_price' => $row['sale_price']
            ];
        }
    }

    return array_values($orders);
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


  /*========================== GET PRICE ====================================*/

 public function getOrderItemsWithPrices(int $orderId): array {
    $sql = "
      SELECT
        order_items.order_id,
        order_items.product_id,
        product.name,
        order_items.quantity,
        product.sale_price AS unit_price,
        (order_items.quantity * product.sale_price) AS total_price_per_item
      FROM
        order_items
      JOIN
        product ON order_items.product_id = product.id
      WHERE
        order_items.order_id = :orderId
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['orderId' => $orderId]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}



/*=========================== GET CART CLIENT ===============================*/
public function getCartByUserId(int $userId, ?int $statusId = null) {
    $latestOrderQuery = "
        SELECT id 
        FROM orders 
        WHERE user_id = :user_id" . 
        ($statusId !== null ? " AND status_id = :status_id" : "") . "
        ORDER BY updated_at DESC 
        LIMIT 1
    ";
    $params = [':user_id' => $userId];
    if ($statusId !== null) {
        $params[':status_id'] = $statusId;
    }
    $req = $this->db->prepare($latestOrderQuery);
    $req->execute($params);
    $orderRow = $req->fetch(PDO::FETCH_ASSOC);
    if (!$orderRow) {
        return null;
    }
    $orderId = $orderRow['id'];
    $query = "
        SELECT 
            o.id AS order_id,
            o.total_price,
            o.status_id,
            o.updated_at,
            oi.id AS item_id,
            oi.product_id,
            p.name AS product_name,
            p.sale_price
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN product p ON oi.product_id = p.id
        WHERE o.id = :order_id
    ";
    $req = $this->db->prepare($query);
    $req->execute([':order_id' => $orderId]);
    $results = $req->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        return null;
    }
    $cart = [
        'order_id' => $results[0]['order_id'],
        'total_price' => $results[0]['total_price'],
        'status_id' => $results[0]['status_id'],
        'updated_at' => $results[0]['updated_at'],
        'items' => []
    ];
    foreach ($results as $row) {
        if ($row['item_id']) {
            $cart['items'][] = [
                'item_id' => $row['item_id'],
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'sale_price' => $row['sale_price']
            ];
        }
    }
    return $cart;
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

  /*========================= CREAT EMPTY CART =============================*/
  public function createEmptyCart(int $userId): void {
    $query = "
        INSERT INTO $this->table (user_id, status_id, total_price, payment_method_id, picture)
        VALUES (:user_id, 1, 0, NULL, NULL)
    ";
    $stmt = $this->db->prepare($query);
    $stmt->execute([
        'user_id' => $userId
    ]);
}

   /*========================= UPDATE STATUS ================================*/

  public function updateStatus(array $data, int $id) {
    $request = "UPDATE $this->table SET ";
    $params = [];
    $fields = [];

    foreach ($data as $key => $value) {
      if (in_array($key, ['status_id'])) {
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