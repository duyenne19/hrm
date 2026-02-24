<?php
// Router file cho PHP built-in server
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

// Nếu là file tĩnh hoặc thư mục tồn tại
if ($uri !== '/' && (is_file($file) || is_dir($file))) {
    return false;
}

// Route kiểm tra phần mở rộng file
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|svg|ico|woff|woff2|ttf|eot|otf)$/', $uri)) {
    http_response_code(404);
    return false;
}

// Default route về index.php
$_SERVER['PHP_SELF'] = '/index.php';
include realpath('index.php');
