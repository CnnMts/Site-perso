<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\OrderModel;
use App\Utils\{Route,HttpException};
use App\Middlewares\{AuthMiddleware};

class Order extends Controller {
  protected object $order;

  public function __construct($param) {
    $this->order = new OrderModel();
  $this->user = $param['user'] ?? null;
    parent::__construct($param);
  }

  /*========================= POST ==========================================*/

  #[Route("POST", "/orders")]
  public function createOrder() {
    $this->order->add($this->body);

    return $this->order->getLast();
  }

  /*========================= GET BY ID =====================================*/

  #[Route("GET", "/orders/:id",
   /* middlewares: [AuthMiddleware::class]*/)]
  
  public function getOrder() {
    return $this->order->get(intval($this->params['id']));
  }


  /*======================== GET CART CLIENT ================================*/
#[Route("GET", "/order/cart/:id"/*, middlewares: [AuthMiddleware::class]*/)]
public function getCartOrder() {
    $userId = intval($this->params['id']);
    $cart = $this->order->getCartByUserId($userId);
    if ($cart === null) {
        http_response_code(200);
        echo json_encode(['cart' => null]);
        exit;
    };
    return $this->order->getCartByUserId($userId);
}



  /*========================= GET ALL =======================================*/

  #[Route("GET", "/orders",
    /*middlewares: [AuthMiddleware::class]*/)]

  public function getOrders() {
      $limit = isset($this->params['limit']) ?
       intval($this->params['limit']) : null;
      return $this->order->getAll($limit);
  }

  /*========================= GET ALL ORDER BY ID USER =====================*/

#[Route("GET", "/ordersUserId/:id")]
public function getAllOrdersByUser()
{
    if (!isset($this->params['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID utilisateur manquant']);
        exit;
    }

    $userId = intval($this->params['id']);
    $orders = $this->order->getAllOrdersByUserId($userId);
    if (empty($orders)) {
        http_response_code(200);
        echo json_encode(['orders' => []]);
        exit;
    }

    return $orders;
}

  /*========================== GET WITH ITEMS ===============================*/

  
  #[Route("GET", "/ordersItems",
    /*middlewares: [AuthMiddleware::class]*/)]

  public function getAllOrders() {
    $orders = $this->order->getAllOrdersWithItems();
    return $orders;
}



/*===========================================================================*/
  #[Route("GET", "/orders/:id/total-price")]
  public function getOrderTotalPrice() {
      $orderId = intval($this->params['id']);
      $total = $this->order->calculateTotalPrice($orderId);
        return ['order_id' => $orderId, 'total_price' => $total];
}


/*===========================================================================*/


  #[Route("PATCH", "/orders/:id/update-total")]
  public function updateOrderTotalPrice() {
      $orderId = intval($this->params['id']);
      $success = $this->order->updateTotalPrice($orderId);
      if ($success) {
        return ['order_id' => $orderId, 'message' => 'Total price updated successfully.'];
      } else {
          http_response_code(500);
          return ['error' => 'Failed to update total price.'];
      }
}



  /*========================= PATCH =========================================*/

  #[Route("PATCH", "/orders/:id", 
   /* middlewares: [AuthMiddleware::class]*/)]

  public function updateOrder() {
    try {
      $id = intval($this->params['id']);
      $data = $this->body;

      # Check if the data is empty
      if (empty($data)) {
        throw new HttpException("Missing parameters for the update.", 400);
      }

      # Check for missing fields
      if (!empty($missingFields)) {
        throw new HttpException(
          "Missing fields: " . implode(", ", $missingFields), 400);
      }

      $this->order->update($data, intval($id));

      # Let's return the updated order
      return $this->order->get($id);
    } catch (HttpException $e) {
      throw $e;
    }
  }

  /*========================= UPDATE STATUS =========================================*/
#[Route("PATCH", "/ordersUpStatus/:id")]
public function updateOrderStatus() {
    try {
        $id = intval($this->params['id']);
        $data = $this->body;
        if (empty($data)) {
            throw new HttpException("Missing parameters for the update.", 400);
        }

        $existingOrder = $this->order->get($id);
        if (!$existingOrder) {
            throw new HttpException("Order not found.", 404);
        }


        if (isset($data['status_id'])) {
            $existingOrder['status_id'] = $data['status_id'];
        }

        $this->order->updateStatus($existingOrder, $id);
        if (intval($existingOrder['status_id']) === 2) {
            $this->order->createEmptyCart($existingOrder['user_id']);
        }

        return $this->order->get($id);
    } catch (HttpException $e) {
        throw $e;
    }
}



  /*========================= TOGGLE =========================================*/
  #[Route("PATCH", "/orders/:id/toggle")]
  public function toggleStatus() {
    $orderId = intval($this->params['id']);
    try {
        $newStatus = $this->order->toggleOrderStatus($orderId);
        header('Content-Type: application/json');
        return['success' => true, 'newStatus' => $newStatus];
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        return['success' => false, 'message' => $e->getMessage()];
    }
  }


  /*========================= DELETE ========================================*/

  #[Route("DELETE", "/orders/:id",
    middlewares: [AuthMiddleware::class])]

  public function deleteOrder() {
    return $this->order->delete(intval($this->params['id']));
  }
}
