<?php
/**
 * Lumina Bookstore - Data Layer & Repository
 * Seamlessly switches between live MySQL and high-fidelity fallback catalog
 */

require_once __DIR__ . '/db.php';

// High-fidelity fallback catalog (used when MySQL is offline or initializing)
$FALLBACK_BOOKS = [
    [
        'id' => 1,
        'title' => 'The Architecture of Tomorrow',
        'author' => 'Dr. Evelyn Vance',
        'category_id' => 4,
        'genre' => 'Technology & AI',
        'price' => 28.50,
        'original_price' => 36.00,
        'rating' => 4.9,
        'reviews_count' => 342,
        'cover_theme' => 'cyber-blue',
        'description' => 'A tour de force exploring neural engineering, autonomous systems, and how machine intelligence is redefining human capability, ethics, and creative consciousness in the next fifty years.',
        'isbn' => '978-0134685991',
        'pages' => 412,
        'format' => 'Hardcover',
        'publisher' => 'Silicon Horizon Books',
        'publish_year' => 2024,
        'in_stock' => 18,
        'is_bestseller' => 1,
        'is_featured' => 1,
        'badge' => 'BESTSELLER'
    ],
    [
        'id' => 2,
        'title' => 'Echoes of the Starlit Sea',
        'author' => 'Julian Thorne',
        'category_id' => 2,
        'genre' => 'Fiction & Literature',
        'price' => 22.95,
        'original_price' => 29.99,
        'rating' => 4.8,
        'reviews_count' => 518,
        'cover_theme' => 'ocean-deep',
        'description' => 'A breathtaking multi-generational tale woven across coastal Portugal and distant lighthouses, celebrating love, forgotten maritime maps, and family secrets hidden beneath tidal tides.',
        'isbn' => '978-0451524935',
        'pages' => 384,
        'format' => 'Hardcover',
        'publisher' => 'Pelican Bay Press',
        'publish_year' => 2023,
        'in_stock' => 14,
        'is_bestseller' => 1,
        'is_featured' => 1,
        'badge' => 'STAFF PICK'
    ],
    [
        'id' => 3,
        'title' => 'Zero to Mastery: Mental Models',
        'author' => 'Marcus Chen',
        'category_id' => 6,
        'genre' => 'Psychology & Mind',
        'price' => 24.00,
        'original_price' => 32.00,
        'rating' => 4.9,
        'reviews_count' => 840,
        'cover_theme' => 'emerald-forest',
        'description' => 'Master 30 battle-tested frameworks used by top strategists, Nobel laureates, and high-stakes problem solvers to cut through noise, conquer cognitive bias, and decide with certainty.',
        'isbn' => '978-0743273565',
        'pages' => 320,
        'format' => 'Paperback',
        'publisher' => 'Keystone Mind Press',
        'publish_year' => 2024,
        'in_stock' => 45,
        'is_bestseller' => 1,
        'is_featured' => 1,
        'badge' => 'HOT DEAL'
    ],
    [
        'id' => 4,
        'title' => 'Neon Horizon: 2099',
        'author' => 'Kira Sterling',
        'category_id' => 3,
        'genre' => 'Sci-Fi & Fantasy',
        'price' => 19.99,
        'original_price' => 24.99,
        'rating' => 4.7,
        'reviews_count' => 276,
        'cover_theme' => 'purple-nebula',
        'description' => 'In a neon-drenched orbital megacity hovering above the Pacific, a renegade data courier unearths a simulated reality glitch that threatens the synthetic hierarchy.',
        'isbn' => '978-0385537858',
        'pages' => 460,
        'format' => 'Paperback',
        'publisher' => 'Starlight Galaxy Imprint',
        'publish_year' => 2024,
        'in_stock' => 8,
        'is_bestseller' => 0,
        'is_featured' => 1,
        'badge' => 'NEW RELEASE'
    ],
    [
        'id' => 5,
        'title' => 'The Resilient Capitalist',
        'author' => 'Jonathan Sterling',
        'category_id' => 5,
        'genre' => 'Business & Wealth',
        'price' => 31.00,
        'original_price' => 39.50,
        'rating' => 4.8,
        'reviews_count' => 412,
        'cover_theme' => 'amber-gold',
        'description' => 'A candid playbook for surviving macroeconomic volatility, deploying asymmetric capital, and building enduring enterprises in hyper-competitive global landscapes.',
        'isbn' => '978-0307277671',
        'pages' => 352,
        'format' => 'Hardcover',
        'publisher' => 'Vanguard Business House',
        'publish_year' => 2023,
        'in_stock' => 22,
        'is_bestseller' => 1,
        'is_featured' => 0,
        'badge' => 'POPULAR'
    ],
    [
        'id' => 6,
        'title' => 'Whispers in the Blackwood',
        'author' => 'Eleanor Finch',
        'category_id' => 7,
        'genre' => 'Mystery & Thriller',
        'price' => 18.50,
        'original_price' => 23.00,
        'rating' => 4.6,
        'reviews_count' => 195,
        'cover_theme' => 'crimson-noir',
        'description' => 'When an enigmatic botanist vanishes inside a secluded Appalachian valley, an insomniac investigator follows a trail of preserved specimens and cryptic journal entries.',
        'isbn' => '978-0143127741',
        'pages' => 310,
        'format' => 'Paperback',
        'publisher' => 'Blackwood Literary',
        'publish_year' => 2024,
        'in_stock' => 12,
        'is_bestseller' => 0,
        'is_featured' => 0,
        'badge' => 'THRILLER'
    ],
    [
        'id' => 7,
        'title' => 'The Quantum Mindset',
        'author' => 'Prof. Aris Thorne',
        'category_id' => 4,
        'genre' => 'Technology & AI',
        'price' => 26.00,
        'original_price' => 34.00,
        'rating' => 4.8,
        'reviews_count' => 310,
        'cover_theme' => 'sapphire-dream',
        'description' => 'Demystifying quantum mechanics, quantum computing paradigms, and probabilistic thinking for founders, software engineers, and curious polymaths.',
        'isbn' => '978-0525559474',
        'pages' => 298,
        'format' => 'Hardcover',
        'publisher' => 'Apex Science Press',
        'publish_year' => 2024,
        'in_stock' => 19,
        'is_bestseller' => 0,
        'is_featured' => 1,
        'badge' => 'TRENDING'
    ],
    [
        'id' => 8,
        'title' => 'The Alchemist of Florence',
        'author' => 'Matteo Rossi',
        'category_id' => 8,
        'genre' => 'Biography & History',
        'price' => 27.50,
        'original_price' => 35.00,
        'rating' => 4.9,
        'reviews_count' => 620,
        'cover_theme' => 'terracotta-sun',
        'description' => 'An intimate portrait of Renaissance polymaths, secret guild workshops, and the feverish race to formulate eternal pigments that shaped European art history.',
        'isbn' => '978-0679722762',
        'pages' => 520,
        'format' => 'Hardcover',
        'publisher' => 'Medici Historical House',
        'publish_year' => 2023,
        'in_stock' => 16,
        'is_bestseller' => 1,
        'is_featured' => 0,
        'badge' => 'EDITOR CHOICE'
    ],
    [
        'id' => 9,
        'title' => 'Deep Work & The Quiet Brain',
        'author' => 'Clara Henderson',
        'category_id' => 6,
        'genre' => 'Psychology & Mind',
        'price' => 21.00,
        'original_price' => 27.00,
        'rating' => 4.7,
        'reviews_count' => 430,
        'cover_theme' => 'sage-zen',
        'description' => 'Scientific strategies to insulate your cognitive attention, enter flow states at will, and eliminate digital dopamine traps in an attention-starved economy.',
        'isbn' => '978-1400033416',
        'pages' => 272,
        'format' => 'Paperback',
        'publisher' => 'Stillwater Books',
        'publish_year' => 2024,
        'in_stock' => 33,
        'is_bestseller' => 0,
        'is_featured' => 0,
        'badge' => 'SELF CARE'
    ],
    [
        'id' => 10,
        'title' => 'Chronicles of the Solar Empire',
        'author' => 'Aiden Zhao',
        'category_id' => 3,
        'genre' => 'Sci-Fi & Fantasy',
        'price' => 25.50,
        'original_price' => 30.00,
        'rating' => 4.9,
        'reviews_count' => 780,
        'cover_theme' => 'cosmic-dark',
        'description' => 'An interstellar epic exploring dynastic rivalries across Jovian colonies, asteroid mining guilds, and the discovery of an ancient Dyson sphere fragment.',
        'isbn' => '978-0062316097',
        'pages' => 624,
        'format' => 'Hardcover',
        'publisher' => 'Starlight Galaxy Imprint',
        'publish_year' => 2023,
        'in_stock' => 11,
        'is_bestseller' => 1,
        'is_featured' => 1,
        'badge' => 'BESTSELLER'
    ],
    [
        'id' => 11,
        'title' => 'The Startup Crucible',
        'author' => 'Rohan Patel & Sarah Kim',
        'category_id' => 5,
        'genre' => 'Business & Wealth',
        'price' => 29.00,
        'original_price' => 38.00,
        'rating' => 4.8,
        'reviews_count' => 510,
        'cover_theme' => 'bronze-slate',
        'description' => 'Raw lessons from 50 founders who navigated near-death startup moments, pivot paralysis, and scale breakdowns to achieve durable enterprise market leadership.',
        'isbn' => '978-0399590504',
        'pages' => 368,
        'format' => 'Hardcover',
        'publisher' => 'Vanguard Business House',
        'publish_year' => 2024,
        'in_stock' => 27,
        'is_bestseller' => 0,
        'is_featured' => 1,
        'badge' => 'FOUNDER PICK'
    ],
    [
        'id' => 12,
        'title' => 'Shadows on Baker Street',
        'author' => 'Arthur C. Campbell',
        'category_id' => 7,
        'genre' => 'Mystery & Thriller',
        'price' => 17.90,
        'original_price' => 22.50,
        'rating' => 4.7,
        'reviews_count' => 340,
        'cover_theme' => 'charcoal-mist',
        'description' => 'A masterfully crafted neo-Victorian detective novel tracing clandestine societies, underground rail vaults, and mechanical enigmas in fog-shrouded London.',
        'isbn' => '978-0345803481',
        'pages' => 344,
        'format' => 'Paperback',
        'publisher' => 'Blackwood Literary',
        'publish_year' => 2023,
        'in_stock' => 15,
        'is_bestseller' => 0,
        'is_featured' => 0,
        'badge' => 'CLASSIC NOIR'
    ]
];

$FALLBACK_CATEGORIES = [
    ['id' => 1, 'name' => 'All Genres', 'slug' => 'all', 'icon' => 'compass'],
    ['id' => 2, 'name' => 'Fiction & Literature', 'slug' => 'fiction', 'icon' => 'feather'],
    ['id' => 3, 'name' => 'Sci-Fi & Fantasy', 'slug' => 'sci-fi', 'icon' => 'rocket'],
    ['id' => 4, 'name' => 'Technology & AI', 'slug' => 'technology', 'icon' => 'cpu'],
    ['id' => 5, 'name' => 'Business & Wealth', 'slug' => 'business', 'icon' => 'trending-up'],
    ['id' => 6, 'name' => 'Psychology & Mind', 'slug' => 'psychology', 'icon' => 'smile'],
    ['id' => 7, 'name' => 'Mystery & Thriller', 'slug' => 'thriller', 'icon' => 'eye'],
    ['id' => 8, 'name' => 'Biography & History', 'slug' => 'biography', 'icon' => 'award']
];

function getCategories() {
    global $conn, $FALLBACK_CATEGORIES;
    if ($conn) {
        try {
            $stmt = $conn->query("SELECT * FROM categories ORDER BY id ASC");
            $rows = $stmt->fetchAll();
            if (!empty($rows)) return $rows;
        } catch (Exception $e) {}
    }
    return $FALLBACK_CATEGORIES;
}

function getBooks($category = 'all', $search = '', $sort = 'featured') {
    global $conn, $FALLBACK_BOOKS;
    
    if ($conn) {
        try {
            $sql = "SELECT b.*, c.name as category_name, c.slug as category_slug 
                    FROM books b 
                    LEFT JOIN categories c ON b.category_id = c.id 
                    WHERE 1=1";
            $params = [];
            
            if (!empty($category) && $category !== 'all') {
                $sql .= " AND (c.slug = :slug OR b.genre LIKE :genre)";
                $params['slug'] = $category;
                $params['genre'] = '%' . $category . '%';
            }
            
            if (!empty($search)) {
                $sql .= " AND (b.title LIKE :s1 OR b.author LIKE :s2 OR b.genre LIKE :s3 OR b.description LIKE :s4)";
                $params['s1'] = "%$search%";
                $params['s2'] = "%$search%";
                $params['s3'] = "%$search%";
                $params['s4'] = "%$search%";
            }
            
            switch ($sort) {
                case 'price-asc':
                    $sql .= " ORDER BY b.price ASC";
                    break;
                case 'price-desc':
                    $sql .= " ORDER BY b.price DESC";
                    break;
                case 'rating':
                    $sql .= " ORDER BY b.rating DESC, b.reviews_count DESC";
                    break;
                case 'newest':
                    $sql .= " ORDER BY b.publish_year DESC, b.id DESC";
                    break;
                default:
                    $sql .= " ORDER BY b.is_featured DESC, b.is_bestseller DESC, b.id ASC";
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            if (!empty($results) || !empty($search) || ($category !== 'all')) {
                return $results;
            }
        } catch (Exception $e) {}
    }
    
    // In-memory filter on fallback list
    $books = $FALLBACK_BOOKS;
    
    if (!empty($category) && $category !== 'all') {
        $books = array_filter($books, function($b) use ($category) {
            return stripos($b['genre'], $category) !== false;
        });
    }
    
    if (!empty($search)) {
        $term = strtolower($search);
        $books = array_filter($books, function($b) use ($term) {
            return strpos(strtolower($b['title']), $term) !== false ||
                   strpos(strtolower($b['author']), $term) !== false ||
                   strpos(strtolower($b['genre']), $term) !== false;
        });
    }
    
    usort($books, function($a, $b) use ($sort) {
        if ($sort === 'price-asc') return $a['price'] <=> $b['price'];
        if ($sort === 'price-desc') return $b['price'] <=> $a['price'];
        if ($sort === 'rating') return $b['rating'] <=> $a['rating'];
        if ($sort === 'newest') return $b['publish_year'] <=> $a['publish_year'];
        return ($b['is_featured'] + $b['is_bestseller']) <=> ($a['is_featured'] + $a['is_bestseller']);
    });
    
    return array_values($books);
}

function getBookById($id) {
    global $conn, $FALLBACK_BOOKS;
    $id = (int)$id;
    
    if ($conn) {
        try {
            $stmt = $conn->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $book = $stmt->fetch();
            if ($book) return $book;
        } catch (Exception $e) {}
    }
    
    foreach ($FALLBACK_BOOKS as $b) {
        if ($b['id'] == $id) return $b;
    }
    return $FALLBACK_BOOKS[0] ?? null;
}

function getFeaturedBooks() {
    $books = getBooks('all');
    return array_filter($books, fn($b) => !empty($b['is_featured']));
}

function getBestsellers() {
    $books = getBooks('all');
    return array_filter($books, fn($b) => !empty($b['is_bestseller']));
}

function saveOrder($orderData, $items) {
    global $conn;
    
    $orderNumber = 'LUM-' . strtoupper(substr(uniqid(), 7)) . '-' . rand(100, 999);
    
    if ($conn) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO orders 
                (order_number, customer_name, customer_email, customer_phone, shipping_address, city, postal_code, payment_method, subtotal, discount, shipping_fee, total_amount, status) 
                VALUES (:num, :name, :email, :phone, :addr, :city, :zip, :pm, :sub, :disc, :ship, :total, 'Confirmed')");
            
            $stmt->execute([
                'num' => $orderNumber,
                'name' => $orderData['name'],
                'email' => $orderData['email'],
                'phone' => $orderData['phone'] ?? '',
                'addr' => $orderData['address'],
                'city' => $orderData['city'],
                'zip' => $orderData['zip'],
                'pm' => $orderData['payment_method'] ?? 'Cash on Delivery',
                'sub' => $orderData['subtotal'],
                'disc' => $orderData['discount'] ?? 0,
                'ship' => $orderData['shipping_fee'] ?? 0,
                'total' => $orderData['total']
            ]);
            
            $orderId = $conn->lastInsertId();
            
            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, title, price, quantity) VALUES (:oid, :bid, :title, :price, :qty)");
            foreach ($items as $item) {
                $itemStmt->execute([
                    'oid' => $orderId,
                    'bid' => $item['id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'qty' => $item['quantity']
                ]);
            }
            
            $conn->commit();
            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
        } catch (Exception $e) {
            if ($conn->inTransaction()) $conn->rollBack();
        }
    }
    
    // Session-based storage fallback
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['orders'])) $_SESSION['orders'] = [];
    
    $orderRecord = [
        'id' => count($_SESSION['orders']) + 1,
        'order_number' => $orderNumber,
        'customer_name' => $orderData['name'],
        'customer_email' => $orderData['email'],
        'shipping_address' => $orderData['address'] . ', ' . $orderData['city'] . ' ' . $orderData['zip'],
        'payment_method' => $orderData['payment_method'] ?? 'Cash on Delivery',
        'subtotal' => $orderData['subtotal'],
        'discount' => $orderData['discount'] ?? 0,
        'shipping_fee' => $orderData['shipping_fee'] ?? 0,
        'total_amount' => $orderData['total'],
        'status' => 'Confirmed',
        'created_at' => date('Y-m-d H:i:s'),
        'items' => $items
    ];
    
    $_SESSION['orders'][] = $orderRecord;
    
    return [
        'success' => true,
        'order_id' => $orderRecord['id'],
        'order_number' => $orderNumber
    ];
}

function getOrders() {
    global $conn;
    if ($conn) {
        try {
            $stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20");
            $orders = $stmt->fetchAll();
            if (!empty($orders)) return $orders;
        } catch (Exception $e) {}
    }
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['orders'] ?? [];
}
?>
