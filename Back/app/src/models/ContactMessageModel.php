<?php

namespace App\Models;

use \PDO;
use stdClass;
use \Exception;
use App\Utils\HttpException;

class ContactMessageModel extends SqlConnect {
  private $table = "contact_messages";

  public function add(array $data) {
    $query = "
      INSERT INTO $this->table (name, email, subject, message)
      VALUES (:name, :email, :subject, :message)
    ";

    $req = $this->db->prepare($query);
    $req->execute($data);
  }

  public function get(int $id) {
    $query = "SELECT * FROM $this->table WHERE id = :id";
    $req = $this->db->prepare($query);
    $req->execute(["id" => $id]);

    if ($req->rowCount() === 0) {
      throw new HttpException("Message not found!", 404);
    }

    return $req->fetch(PDO::FETCH_ASSOC);
  }

  public function getAll(?int $limit = null) {
    $query = "SELECT * FROM $this->table ORDER BY created_at DESC";
    
    if ($limit !== null) {
      $query .= " LIMIT :limit";
    }

    $req = $this->db->prepare($query);
    
    if ($limit !== null) {
      $req->bindValue(':limit', $limit, PDO::PARAM_INT);
    }

    $req->execute();

    return $req->fetchAll(PDO::FETCH_ASSOC);
  }

  public function delete(int $id) {
    $req = $this->db->prepare("DELETE FROM $this->table WHERE id = :id");
    $req->execute(["id" => $id]);

    return new stdClass();
  }
}
