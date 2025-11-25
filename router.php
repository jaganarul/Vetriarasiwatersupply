<?php
// Simple router: maps pretty routes to existing PHP files
require_once __DIR__ . '/init.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// strip base_url if present
$base = rtrim($base_url ?? '', '/');
if ($base && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
    if ($uri === '') $uri = '/';
}

$uri = '/' . trim($uri, '/');

// route map (basic)
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

// product detail: /product/123
if (preg_match('#^/product/(\d+)$#', $uri, $m)){
    $_GET['id'] = $m[1];
    require __DIR__ . '/product.php';
    exit;
}

// categories: /category/Category-Name
if (preg_match('#^/category/(.+)$#', $uri, $m)){
    $_GET['category'] = urldecode($m[1]);
    require __DIR__ . '/index.php';
    exit;
}

// static pages we support mapping for
$mapping = [
    '/products' => '/index.php',
    '/cart' => '/cart.php',
    '/checkout' => '/checkout.php',
    '/payments' => '/payments.php',
    '/login' => '/login.php',
    '/register' => '/register.php',
    '/logout' => '/logout.php',
    '/profile' => '/profile.php',
    '/track' => '/track.php',
    '/about' => '/about.php',
    '/contact' => '/contact.php',
    '/admin' => '/admin/index.php'
];

if (isset($mapping[$uri])){
    require __DIR__ . $mapping[$uri];
    exit;
}

// admin sub-routes: /admin/products -> /admin/products.php
if (strpos($uri, '/admin/') === 0) {
    $adminPath = substr($uri, 6); // strip /admin
    $target = __DIR__ . '/admin/' . ltrim($adminPath, '/');
    // translate '/admin/products' to '/admin/products.php'
    if (is_dir(__DIR__ . '/admin/' . $adminPath)){
        require __DIR__ . '/admin/index.php';
        exit;
    }
    if (file_exists($target . '.php')){
        require $target . '.php';
        exit;
    }
}

// fallback: try to serve as direct PHP file (strip leading slash)
$try = __DIR__ . $uri;
if (file_exists($try) && is_file($try)){
    require $try;
    exit;
}

// not found
http_response_code(404);
echo "<h1>404 Not Found</h1><p>The requested URL was not found on this server.</p>";
exit;
