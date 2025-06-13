<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\OrderItemModel;
use App\Utils\{Route,HttpException};
use App\Middlewares\{AuthMiddleware,RoleMiddleware, Roles};

class OrderItem extends Controller {
  protected object $orderItem;

  public function __construct($param) {
    $this->orderItem = new OrderItemModel();

    parent::__construct($param);
  }

  private $user;

public function setUser($user) {
    $this->user = $user;
}

  /*========================= POST ==========================================*/

  #[Route("POST", "/AddorderItems")]
public function createOrderItem() {
    $data = $this->body;

    if (empty($data['product_id']) || empty($data['order_id']) || empty($data['name'])) {
        throw new HttpException("Champs requis manquants", 400);
    }

    $this->orderItem->add($data);

    return $this->orderItem->getLast();
}


  /*========================= GET BY ID =====================================*/

  #[Route("GET", "/orderItems/:id",
    middlewares: [AuthMiddleware::class])]
  
  public function getOrderItem() {
    return $this->orderItem->get(intval($this->params['id']));
  }

  /*========================= GET ALL =====================================*/

  #[Route("GET", "/orderItems",
  /*middlewares: [AuthMiddleware::class]*/)]
  public function getOrderItems() {
      $limit = isset($this->params['limit']) ? 
      intval($this->params['limit']) : null;
      return $this->orderItem->getAll($limit);
  }

  /*========================= GET BY ID =====================================*/

  #[Route("PATCH", "/orderItems/:id",
    middlewares: [AuthMiddleware::class])]
  public function updateOrderItem() {
    try {
      $id = intval($this->params['id']);
      $data = $this->body;

      if (empty($data)) {
        throw new HttpException("Missing parameters for the update.", 400);
      }
      $missingFields = array_diff(
        $this->orderItem->authorized_fields_to_update, array_keys($data));
      if (!empty($missingFields)) {
        throw new HttpException(
          "Missing fields: " . implode(", ", $missingFields), 400);
      }

      $this->orderItem->update($data, intval($id));
      return $this->orderItem->get($id);
    } catch (HttpException $e) {
      throw $e;
    }
  }

/*=====================  GET BY ORDER ID ====================================*/
  #[Route("GET", "/orderItems/byOrder/:orderId",
  middlewares: [AuthMiddleware::class])]
public function getItemsByOrderId() {
    $orderId = intval($this->params['orderId']);
    if ($orderId <= 0) {
        throw new HttpException("orderId invalide", 400);
    }
    return $this->orderItem->getByOrderId($orderId);
}


  /*========================= DELETE ========================================*/

  #[Route("DELETE", "/orderItems/:id",
    middlewares: [AuthMiddleware::class, 
    [RoleMiddleware::class, Roles::ROLE_ADMIN]])]
  public function deleteOrderItem() {
    return $this->orderItem->delete(intval($this->params['id']));
  }
}