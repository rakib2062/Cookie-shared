<?php
// ---------------- CONFIG ----------------
$allowedOrigins = [
    'http://127.0.0.1:8002',
    'http://127.0.0.1:8003',
];
$cookieName = 'tracker_id';
$cookieDomain = 'http://127.0.0.1:8001'; // must match your setup
$secure = true;
$httpOnly = false;
$sameSite = 'None';

// ---------------- HELPERS ----------------
function jsonResponse($data, $status = 200, $origin = null) {
    header('Content-Type: application/json');
    http_response_code($status);
    if ($origin) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
    }
    echo json_encode($data);
    exit;
}

function makeCookie($name, $value, $domain, $secure, $httpOnly, $sameSite) {
    setcookie($name, $value, [
        'expires'  => time() + (60 * 60 * 24 * 365),
        'path'     => '/',
        'domain'   => $domain,
        'secure'   => $secure,
        'httponly' => $httpOnly,
        'samesite' => $sameSite,
    ]);
}

// ---------------- ROUTING ----------------
$requestUri = strtok($_SERVER["REQUEST_URI"], '?');
$method = $_SERVER['REQUEST_METHOD'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;

// Handle preflight
if ($method === 'OPTIONS') {
    if ($origin && in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }
    http_response_code(204);
    exit;
}

// ---------------- /track ----------------
if ($requestUri === '/track') {
    if ($origin && !in_array($origin, $allowedOrigins)) {
        jsonResponse(['error' => 'Origin not allowed'], 403);
    }

    $trackerId = $_COOKIE[$cookieName] ?? null;
    if (!$trackerId) {
        // Generate unique ID (UUID-like)
        $trackerId = bin2hex(random_bytes(8));
        makeCookie($cookieName, $trackerId, $cookieDomain, $secure, $httpOnly, $sameSite);
    }

    jsonResponse(['tracker_id' => $trackerId], 200, $origin);
}

// ---------------- /redirect-sync ----------------
if ($requestUri === '/redirect-sync') {
    $originParam = $_GET['origin'] ?? '';
    if (!$originParam || !in_array($originParam, $allowedOrigins)) {
        http_response_code(400);
        echo 'Invalid or missing origin';
        exit;
    }

    $trackerId = $_COOKIE[$cookieName] ?? bin2hex(random_bytes(8));
    makeCookie($cookieName, $trackerId, $cookieDomain, $secure, $httpOnly, $sameSite);

    // Safe redirect
    $redirectUrl = rtrim($originParam, '/') . '?tracker_id=' . urlencode($trackerId);
    header("Location: $redirectUrl", true, 302);
    exit;
}

// ---------------- DEFAULT ----------------
http_response_code(404);
echo "Not Found";
