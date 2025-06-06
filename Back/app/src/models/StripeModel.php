<?php

namespace App\Models;

use \PDO;
use stdClass;
use \Exception;
use App\Utils\{HttpException};
use Stripe\Stripe;
use Stripe\Checkout\Session;


class StripeModel extends SqlConnect
{
    public function __construct()
{
    parent::__construct();

    try {
        $secret = $_ENV['STRIPE_SECRET'] ?? '';
        if (empty($secret)) {
            throw new \Exception('Stripe secret key missing');
        }
        \Stripe\Stripe::setApiKey($secret);
    } catch (\Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}


    /**
     * Crée une session de checkout Stripe pour un utilisateur donné.
     * @param int $userId
     * @return Session
     * @throws Exception
     */
    public function createCheckoutSession(int $userId): Session
    {
        // Exemple : récupère les items du panier depuis la DB (à adapter selon ta DB)
        $cartItems = $this->getCartItemsByUserId($userId);
        if (empty($cartItems)) {
            throw new HttpException("Panier vide pour l'utilisateur $userId.", 400);
        }

        $lineItems = [];
        foreach ($cartItems as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['product_name'],
                    ],
                    'unit_amount' => intval($item['sale_price'] * 100), // prix en centimes
                ],
                'quantity' => intval($item['quantity']),
            ];
        }

        // Création de la session Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $_ENV['APP_URL'] . '/stripe/checkout-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['APP_URL'] . '/cart',
            'metadata' => [
                'user_id' => $userId,
            ],
        ]);

        return $session;
    }

    /**
     * Traite la réussite du paiement à partir d'un ID de session Stripe.
     * @param string $sessionId
     * @return array
     * @throws Exception
     */
    public function handleSuccess(string $sessionId): array
    {
        // Récupération de la session depuis Stripe
        $session = Session::retrieve($sessionId);
        if (!$session || $session->payment_status !== 'paid') {
            throw new HttpException("Session non valide ou paiement non effectué.", 400);
        }

        // Exemple : ici tu pourrais marquer la commande comme payée dans ta DB
        $userId = $session->metadata->user_id ?? null;
        if (!$userId) {
            throw new HttpException("User ID manquant dans la session Stripe.", 400);
        }

        // Mise à jour en base (à adapter)
        $this->markOrderPaidByUserId($userId);

        return [
            'success' => true,
            'message' => "Paiement confirmé pour l'utilisateur $userId.",
            'session' => $session,
        ];
    }

    /**
     * Récupère les items du panier pour un utilisateur.
     * À adapter selon ta table et ta logique.
     */
    private function getCartItemsByUserId(int $userId): array
    {
        $query = "SELECT product_name, sale_price, quantity FROM order_items WHERE user_id = :user_id AND order_status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Exemple méthode pour marquer la commande comme payée.
     * À adapter selon ta logique.
     */
    private function markOrderPaidByUserId(int $userId): void
    {
        $query = "UPDATE orders SET order_status = 'paid' WHERE user_id = :user_id AND order_status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);
    }
}
