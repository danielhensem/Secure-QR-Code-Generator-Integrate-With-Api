<?php
session_start();

if (!isset($_SESSION['proxy_url'])) {
    http_response_code(403);
    exit("Access denied");
}

$url = $_SESSION['proxy_url'];
unset($_SESSION['proxy_url']); // one-time access

// Fetch URL content server-side
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'SecureQRSystem/1.0'
]);

$content = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($content === false) {
    exit("Failed to load content.");
}

// Send content WITHOUT exposing URL
header("Content-Type: $contentType");
echo $content;
