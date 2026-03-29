<?php
// api/_helpers.php
// Include at the top of every API endpoint.

// Only allow XHR from this app (basic CSRF mitigation)
if (
    !isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ||
    strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) !== "xmlhttprequest"
) {
    http_response_code(403);
    echo json_encode(["error" => "Forbidden"]);
    exit;
}

header("Content-Type: application/json");
header("X-Content-Type-Options: nosniff");

function jsonBody(): array {
    $raw = file_get_contents("php://input");
    return json_decode($raw, true) ?? [];
}

function jsonOut(mixed $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function sanitize(string $val): string {
    return trim(strip_tags($val));
}
