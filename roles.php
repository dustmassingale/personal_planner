<?php
// api/roles.php
require __DIR__ . "/_helpers.php";
require __DIR__ . "/../db/connection.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $stmt = $db->query("SELECT id, name, description, impact, created_at FROM roles ORDER BY name");
    jsonOut($stmt->fetchAll());
}

if ($method === "POST") {
    $data   = jsonBody();
    $name   = sanitize($data["name"]   ?? "");
    $desc   = sanitize($data["description"] ?? "");
    $impact = sanitize($data["impact"] ?? "");
    if ($name === "") jsonOut(["error" => "Name is required"], 422);

    $stmt = $db->prepare("INSERT INTO roles (name, description, impact) VALUES (?, ?, ?)");
    $stmt->execute([$name, $desc, $impact]);
    jsonOut(["success" => true, "id" => $db->lastInsertId()], 201);
}

if ($method === "DELETE") {
    $data = jsonBody();
    $id   = (int)($data["id"] ?? 0);
    if (!$id) jsonOut(["error" => "Invalid id"], 422);

    $db->prepare("DELETE FROM roles WHERE id = ?")->execute([$id]);
    jsonOut(["success" => true]);
}

jsonOut(["error" => "Method not allowed"], 405);
