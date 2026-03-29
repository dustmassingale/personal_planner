<?php
// api/goals.php
require __DIR__ . "/_helpers.php";
require __DIR__ . "/../db/connection.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $stmt = $db->query(
        "SELECT g.id, g.value_id, g.category, g.description, g.status, g.created_at,
                v.name AS value_name
         FROM goals g
         LEFT JOIN user_values v ON v.id = g.value_id
         ORDER BY g.status, g.created_at DESC"
    );
    jsonOut($stmt->fetchAll());
}

if ($method === "POST") {
    $data     = jsonBody();
    $desc     = sanitize($data["description"] ?? "");
    $category = sanitize($data["category"]    ?? "Other");
    $status   = sanitize($data["status"]      ?? "active");
    $value_id = isset($data["value_id"]) ? (int)$data["value_id"] : null;

    if ($desc === "") jsonOut(["error" => "Description is required"], 422);

    $stmt = $db->prepare(
        "INSERT INTO goals (value_id, category, description, status) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$value_id, $category, $desc, $status]);
    jsonOut(["success" => true, "id" => $db->lastInsertId()], 201);
}

if ($method === "DELETE") {
    $data = jsonBody();
    $id   = (int)($data["id"] ?? 0);
    if (!$id) jsonOut(["error" => "Invalid id"], 422);

    $db->prepare("DELETE FROM goals WHERE id = ?")->execute([$id]);
    jsonOut(["success" => true]);
}

jsonOut(["error" => "Method not allowed"], 405);
