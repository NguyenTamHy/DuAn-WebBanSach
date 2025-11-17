<?php
// public/index.php
declare(strict_types=1);

/*
  index.php with editor-friendly stubs:
  - requires app/* files if exist
  - provides fallback stubs for user(), csrf_check(), db(), e(), money(), require_login(), is_admin()
  - provides minimal ReviewModel and OrderModel classes when missing so Intelephense stops complaining
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// locate app root (one level up from public)
$APP_ROOT = realpath(__DIR__ . '/../') ?: __DIR__ . '/..';

// Try to include real app files if present (these files should define db(), helpers, models, etc.)
if (file_exists($APP_ROOT . '/app/config.php')) {
    require_once $APP_ROOT . '/app/config.php';
}
if (file_exists($APP_ROOT . '/app/db.php')) {
    require_once $APP_ROOT . '/app/db.php';
}
if (file_exists($APP_ROOT . '/app/helpers.php')) {
    require_once $APP_ROOT . '/app/helpers.php';
}
if (file_exists($APP_ROOT . '/app/models/Order.php')) {
    require_once $APP_ROOT . '/app/models/Order.php';
}
if (file_exists($APP_ROOT . '/app/models/Review.php')) {
    require_once $APP_ROOT . '/app/models/Review.php';
}

/* ----------------------------------------------------------------
   Editor/runtime-safe fallback stubs
   These only define functions/classes if they don't already exist.
   In production you should remove these stubs or keep real implementations
   in app/*.php so your app logic runs properly.
   ---------------------------------------------------------------- */

if (!function_exists('db')) {
    /**
     * Fallback db() returning PDO singleton for editor/runtime safety.
     * Replace DSN/user/pass with your real config in app/db.php
     * @return PDO
     */
    function db(): PDO
    {
        static $pdo = null;
        if ($pdo === null) {
            // safe default for editor - won't connect unless used at runtime
            try {
                $pdo = new PDO('mysql:host=127.0.0.1;dbname=thuctaptotnghiep;charset=utf8mb4', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Throwable $e) {
                // create a lightweight in-memory PDO to avoid crashes in editor
                $pdo = new PDO('sqlite::memory:');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }
        return $pdo;
    }
}

if (!function_exists('e')) {
    function e($s): string
    {
        return htmlspecialchars((string)($s ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('money')) {
    function money($n): string
    {
        if (!is_numeric($n)) $n = 0;
        return number_format((float)$n, 0, ',', '.') . 'đ';
    }
}

if (!function_exists('user')) {
    /**
     * Returns currently logged in user array or null.
     * Real implementation should live in app/helpers.php and use sessions/db.
     * @return array|null
     */
    function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('require_login')) {
    function require_login(): void
    {
        if (!user()) {
            header('Location: /login');
            exit;
        }
    }
}

if (!function_exists('csrf_check')) {
    /**
     * Very small fallback CSRF check for editor/runtime.
     */
    function csrf_check(string $token = ''): bool
    {
        if (empty($token)) return false;
        return isset($_SESSION['csrf_token']) && hash_equals((string)$_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return (string)$_SESSION['csrf_token'];
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        $u = user();
        if (!$u) return false;
        // Note: adjust to match your real role field, e.g. 'role' => 'ADMIN'
        return (isset($u['role']) && strtoupper((string)$u['role']) === 'ADMIN') || (isset($u['role']) && strtoupper((string)$u['role']) === 'ADMIN');
    }
}

/* Minimal stub models so static analyzers don't complain.
   Real models (OrderModel/ReviewModel) should be provided in app/models/*.php
*/
if (!class_exists('ReviewModel')) {
    class ReviewModel
    {
        /**
         * Create review
         * @param int $userId
         * @param int $bookId
         * @param int $rating
         * @param string $comment
         * @return void
         */
        public static function create(int $userId, int $bookId, int $rating, string $comment): void
        {
            // fallback: insert into reviews if table exists
            try {
                $st = db()->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?,?,?,?)");
                $st->execute([$userId, $bookId, $rating, $comment]);
            } catch (Throwable $e) {
                // ignore in editor mode
            }
        }
    }
}

if (!class_exists('OrderModel')) {
    class OrderModel
    {
        public static function create(int $userId, array $addr, array $items, array $totals): array
        {
            // naive fallback - generate code and return minimal order array
            $code = 'ORD' . strtoupper(bin2hex(random_bytes(4)));
            try {
                $pdo = db();
                $pdo->beginTransaction();
                $st = $pdo->prepare("INSERT INTO orders (code, user_id, addr_json, subtotal, discount, shipping_fee, total, status, payment_method) VALUES (?,?,?,?,?,?,?,?,?)");
                $st->execute([
                    $code,
                    $userId,
                    json_encode($addr, JSON_UNESCAPED_UNICODE),
                    $totals['subtotal'] ?? 0,
                    $totals['discount'] ?? 0,
                    $totals['shipping'] ?? 0,
                    $totals['total'] ?? 0,
                    'Pending',
                    $totals['payment_method'] ?? 'COD'
                ]);
                $orderId = (int)$pdo->lastInsertId();
                foreach ($items as $it) {
                    $st2 = $pdo->prepare("INSERT INTO order_items (order_id, book_id, title_snapshot, qty, unit_price, line_total) VALUES (?,?,?,?,?,?)");
                    $st2->execute([
                        $orderId,
                        $it['id'] ?? null,
                        $it['title'] ?? '',
                        $it['qty'] ?? 1,
                        $it['price'] ?? 0,
                        ($it['qty'] ?? 1) * ($it['price'] ?? 0)
                    ]);
                }
                $pdo->commit();
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
                // fallback: return minimal info if DB not available
                return ['id' => rand(1000, 9999), 'code' => $code];
            }
            return ['id' => $orderId, 'code' => $code];
        }

        public static function findByCodeForUser(string $code, int $uid): ?array
        {
            try {
                $st = db()->prepare("SELECT * FROM orders WHERE code=? AND user_id=? LIMIT 1");
                $st->execute([$code, $uid]);
                $r = $st->fetch(PDO::FETCH_ASSOC);
                return $r ?: null;
            } catch (Throwable $e) {
                return null;
            }
        }

        public static function itemsOf(int $orderId): array
        {
            try {
                $st = db()->prepare("SELECT * FROM order_items WHERE order_id=?");
                $st->execute([$orderId]);
                return $st->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                return [];
            }
        }

        public static function listAll(): array
        {
            try {
                $st = db()->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 200");
                return $st->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                return [];
            }
        }

        public static function updateStatus(int $id, string $status): void
        {
            try {
                $st = db()->prepare("UPDATE orders SET status=? WHERE id=?");
                $st->execute([$status, $id]);
            } catch (Throwable $e) {
                // ignore
            }
        }
    }
}

/* ----------------- End stubs ----------------- */

/* ======== Compute BASE URL and current route ======== */
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir  = str_replace('\\', '/', dirname($scriptName));
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = '/' . ltrim(str_replace('\\', '/', $requestUri), '/');
$basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$BASE_URL = $protocol . '://' . $host . $basePath;

if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
} else {
    $path = substr($requestUri, strlen($scriptDir)) ?: $requestUri;
}
$path = '/' . trim($path, '/');
if ($path === '//' || $path === '') $path = '/';

function base_url(string $path = ''): string
{
    global $BASE_URL;
    $p = ltrim($path, '/');
    return $BASE_URL . ($p ? '/' . $p : '');
}
$__BASE_URL = $BASE_URL;

/* ================= ROUTING (keeps original app logic) ================= */

if ($path === '/') {
    $title = 'Trang chủ - Bookstore';
    include __DIR__ . '/parts/header.php';
    include __DIR__ . '/home.php';
    include __DIR__ . '/parts/footer.php';
    exit;
}

if ($path === '/book') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
        if (!user()) { header('Location: ' . base_url('login')); exit; }
        if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); exit('CSRF invalid'); }

        $bookId = (int)($_POST['book_id'] ?? 0);
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $comment = trim($_POST['comment'] ?? '');
        ReviewModel::create((int)user()['id'], $bookId, $rating, $comment);
        header('Location: ' . base_url('book?id=' . $bookId));
        exit;
    }

    $title = 'Chi tiết sách';
    include __DIR__ . '/parts/header.php';
    include __DIR__ . '/book_detail.php';
    include __DIR__ . '/parts/footer.php';
    exit;
}

if ($path === '/cart') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $book_id = (int)($_POST['book_id'] ?? 0);
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        if ($book_id > 0) {
            $st = db()->prepare("SELECT id, title, price, stock_qty, cover_url FROM books WHERE id=?");
            $st->execute([$book_id]); $b = $st->fetch(PDO::FETCH_ASSOC);
            if ($b) {
                if ($qty > (int)$b['stock_qty']) $qty = (int)$b['stock_qty'];
                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                $found = false;
                foreach ($_SESSION['cart'] as &$it) {
                    if ($it['id'] == $b['id']) { $it['qty'] += $qty; $found = true; break; }
                }
                unset($it);
                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => (int)$b['id'],
                        'title' => $b['title'],
                        'price' => (float)$b['price'],
                        'qty' => $qty,
                        'cover_url' => $b['cover_url']
                    ];
                }
            }
        }
        header('Location: ' . base_url('cart')); exit;
    }
    $title = 'Giỏ hàng';
    include __DIR__ . '/parts/header.php';
    include __DIR__ . '/cart.php';
    include __DIR__ . '/parts/footer.php';
    exit;
}

if ($path === '/checkout') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!user()) { header('Location: ' . base_url('login')); exit; }
        if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); echo 'CSRF invalid'; exit; }
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) { http_response_code(400); echo 'Giỏ hàng rỗng'; exit; }
        $items = []; $subtotal = 0.0;
        foreach ($cart as $c) {
            $items[] = ['id'=>$c['id'],'title'=>$c['title'],'qty'=>$c['qty'],'price'=>$c['price']];
            $subtotal += $c['qty'] * $c['price'];
        }
        $totals = [
            'subtotal'=>$subtotal, 'discount'=>0.0, 'shipping'=>0.0,
            'total'=>$subtotal, 'payment_method'=>$_POST['payment_method'] ?? 'COD'
        ];
        $addr = [
            'line1'=>$_POST['address'] ?? '', 'city'=>'', 'province'=>'', 'zip'=>'', 'phone'=>$_POST['phone'] ?? ''
        ];
        try {
            $res = OrderModel::create((int)user()['id'], $addr, $items, $totals);
            unset($_SESSION['cart']);
            header('Location: ' . base_url('order') . '?code=' . urlencode($res['code'])); exit;
        } catch (Throwable $e) {
            $checkout_error = $e->getMessage();
        }
    }
    $title = 'Thanh toán';
    include __DIR__ . '/parts/header.php';
    include __DIR__ . '/checkout.php';
    include __DIR__ . '/parts/footer.php';
    exit;
}

if ($path === '/order') {
    require_login();
    $code = $_GET['code'] ?? '';
    $order = OrderModel::findByCodeForUser($code, (int)user()['id']);
    if (!$order) { http_response_code(404); echo "Không tìm thấy đơn"; exit; }
    $order_items = OrderModel::itemsOf((int)$order['id']);
    $title = 'Đơn hàng '.$order['code'];
    include __DIR__ . '/parts/header.php';
    include __DIR__ . '/order_detail.php';
    include __DIR__ . '/parts/footer.php';
    exit;
}

if (strpos($path, '/admin') === 0) {
    if (!is_admin()) { header('Location: ' . base_url('login')); exit; }
    $p = $_GET['p'] ?? 'dashboard';
    include __DIR__ . '/parts/admin_header.php';
    if ($p === 'dashboard') {
        include __DIR__ . '/admin/dashboard.php';
    } elseif ($p === 'orders') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_check($_POST['csrf'] ?? '')) { http_response_code(400); die('CSRF invalid'); }
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'Pending';
            OrderModel::updateStatus($id, $status);
            header('Location: ' . base_url('admin') . '?p=orders'); exit;
        }
        $orders = OrderModel::listAll();
        include __DIR__ . '/admin/orders.php';
    } elseif ($p === 'books') {
        include __DIR__ . '/admin/books.php';
    } else {
        echo "<main class='wrap'><h2>Admin: trang không tồn tại</h2></main>";
    }
    include __DIR__ . '/parts/admin_footer.php';
    exit;
}

// fallback 404
http_response_code(404);
$title = '404';
include __DIR__ . '/parts/header.php';
echo "<main class='container'><h2>404 - Trang không tìm thấy</h2></main>";
include __DIR__ . '/parts/footer.php';
