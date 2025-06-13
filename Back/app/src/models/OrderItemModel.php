<?php

namespace App\Models;

use \PDO;
use stdClass;

class OrderItemModel extends SqlConnect {
  private $table = "order_items";

  /*========================= ADD ===========================================*/

 public function add(array $data) {
    try {
        $requiredFields = ['product_id', 'order_id', 'name', 'picture'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new Exception("Champ requis manquant : $field");
            }
        }

        $sqlInsert = "INSERT INTO order_items (product_id, order_id, name, picture) 
                      VALUES (:product_id, :order_id, :name, :picture)";
        $stmtInsert = $this->db->prepare($sqlInsert);
        $stmtInsert->execute([
            ':product_id' => $data['product_id'],
            ':order_id' => $data['order_id'],
            ':name' => $data['name'],
            ':picture' => $data['picture'],
        ]);
        $sqlTotal = "
            SELECT SUM(p.sale_price) AS total
            FROM order_items oi
            JOIN product p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute([':order_id' => $data['order_id']]);
        $result = $stmtTotal->fetch();
        $total = $result['total'] ?? 0;

        $sqlUpdateOrder = "UPDATE orders SET total_price = :total WHERE id = :order_id";
        $stmtUpdate = $this->db->prepare($sqlUpdateOrder);
        $stmtUpdate->execute([
            ':total' => $total,
            ':order_id' => $data['order_id'],
        ]);

        return true;
    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    } catch (Exception $e) {
        throw new Exception("Erreur : " . $e->getMessage());
    }
}

  /*========================= GET BY ID  ====================================*/

  public function get(int $id) {
    $req = $this->db->prepare("SELECT * FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);

    return $req->rowCount() > 0 ? 
      $req->fetch(PDO::FETCH_ASSOC) : new stdClass();
  }


 /*============================= GET BY ORDER ID ============================*/
  public function getByOrderId(int $orderId): array {
    $sql = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':order_id' => $orderId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

  /*========================= DELETE ========================================*/

  public function delete(int $id) {
    $req = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);
    return new stdClass();
  }
}