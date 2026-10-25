<?php
/**
 * Lumina Bookstore - Lightweight REST API Endpoint for AJAX Requests
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/data.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_books':
        $cat = $_GET['category'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'featured';
        $books = getBooks($cat, $search, $sort);
        echo json_encode(['success' => true, 'count' => count($books), 'data' => $books]);
        break;

    case 'get_book':
        $id = (int)($_GET['id'] ?? 0);
        $book = getBookById($id);
        if ($book) {
            echo json_encode(['success' => true, 'data' => $book]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Book not found']);
        }
        break;

    case 'checkout':
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $customer = $input['customer'] ?? null;
        $items = $input['items'] ?? [];
        
        if (empty($customer) || empty($items)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid order details or cart is empty']);
            exit;
        }

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['price']) * intval($item['quantity']);
        }

        $discount = floatval($customer['discount'] ?? 0);
        $shipping = ($subtotal >= 35 || $subtotal == 0) ? 0 : 4.99;
        $total = max(0, $subtotal - $discount + $shipping);

        $orderData = [
            'name' => trim($customer['name'] ?? 'Guest Customer'),
            'email' => trim($customer['email'] ?? 'guest@example.com'),
            'phone' => trim($customer['phone'] ?? ''),
            'address' => trim($customer['address'] ?? 'Not provided'),
            'city' => trim($customer['city'] ?? 'City'),
            'zip' => trim($customer['zip'] ?? '00000'),
            'payment_method' => $customer['paymentMethod'] ?? 'Cash on Delivery',
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_fee' => $shipping,
            'total' => $total
        ];

        $res = saveOrder($orderData, $items);
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $res['order_id'],
            'order_number' => $res['order_number'],
            'summary' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => $shipping,
                'total' => $total,
                'item_count' => count($items)
            ]
        ]);
        break;

    case 'newsletter':
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Please provide a valid email address']);
            exit;
        }
        echo json_encode([
            'success' => true,
            'message' => 'Welcome to the Lumina Reader Circle! Check your inbox for your 15% discount code: READ15'
        ]);
        break;

    case 'system_status':
        echo json_encode([
            'success' => true,
            'db_connected' => isDbConnected(),
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'XAMPP Local Server'
        ]);
        break;

    default:
        echo json_encode([
            'success' => true,
            'message' => 'Lumina Bookstore API ready',
            'endpoints' => ['get_books', 'get_book', 'checkout', 'newsletter', 'system_status']
        ]);
        break;
}
?>
