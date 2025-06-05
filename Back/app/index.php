<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';
use Dotenv\Dotenv;
use App\Utils\JWT;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
putenv("JWT_SECRET=" . $_ENV['JWT_SECRET']);
file_put_contents('/tmp/debug_jwt_secret.txt', getenv('JWT_SECRET') ?: 'null');
JWT::initialize();


$allowedOrigins = [
    'http://localhost:5173',
    'http://127.0.0.1:5173'
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

use App\Router;
use App\Controllers\{
    Auth,
    Category,
    Order,
    OrderItem,
    OrderStatus,
    PaymentMethod,
    Product,
    Role,
    User,
    ContactMessage
};

$controllers = [
    Auth::class,
    Category::class,
    Order::class,
    OrderItem::class,
    OrderStatus::class,
    PaymentMethod::class,
    Product::class,
    Role::class,
    User::class,
    ContactMessage::class,
];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
error_log("URI extrait: " . $uri);

$router = new Router();
$router->registerControllers($controllers);
$router->run();
