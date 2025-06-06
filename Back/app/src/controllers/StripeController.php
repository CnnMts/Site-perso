<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Utils\Route;
use App\Utils\HttpException;
use App\Middlewares\{AuthMiddleware,RoleMiddleware, Roles};
use App\Models\StripeModel;

class Stripe extends Controller {
    protected StripeModel $stripe;
    protected mixed $user;

    public function __construct($param) {
        $this->stripe = new StripeModel(); 
        $this->user = $param['user'] ?? null;
        parent::__construct($param);
    }

    #[Route("GET", "/stripe/checkout-session/:id")]
    public function createCheckoutSession() {
        $userId = intval($this->params['id']);

        try {
            $session = $this->stripe->createCheckoutSession($userId);
            echo json_encode(['sessionId' => $session->id]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    #[Route("GET", "/stripe/checkout-success/:session_id")]
    public function checkoutSuccess() {
        $sessionId = $this->params['session_id'];

        try {
            $result = $this->stripe->handleSuccess($sessionId);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
