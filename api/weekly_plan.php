<?php
// api/weekly_plan.php
require __DIR__ . "/_helpers.php";
require __DIR__ . "/../db/connection.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $weekStart = sanitize($_GET["week_start"] ?? "");
    if ($weekStart && preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStart)) {
        $stmt = $db->prepare(
            "SELECT * FROM weekly_plan WHERE week_start = ? ORDER BY CASE day
             WHEN 'Monday'    THEN 1 WHEN 'Tuesday'   THEN 2 WHEN 'Wednesday' THEN 3
             WHEN 'Thursday'  THEN 4 WHEN 'Friday'    THEN 5 WHEN 'Saturday'  THEN 6
             WHEN 'Sunday'    THEN 7 END"
        );
        $stmt->execute([$weekStart]);
    } else {
        $stmt = $db->query("SELECT * FROM weekly_plan ORDER BY week_start DESC, id DESC");
    }
    jsonOut($stmt->fetchAll());
}

if ($method === "POST") {
    $data      = jsonBody();
    $weekStart = sanitize($data["week_start"] ?? "");
    $day       = sanitize($data["day"]        ?? "");
    if (!$weekStart || !$day) jsonOut(["error" => "week_start and day are required"], 422);

    $stmt = $db->prepare(
        "INSERT INTO weekly_plan (week_start, day, social, spiritual, physical, learning)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $weekStart,
        $day,
        sanitize($data["social"]    ?? ""),
        sanitize($data["spiritual"] ?? ""),
        sanitize($data["physical"]  ?? ""),
        sanitize($data["learning"]  ?? ""),
    ]);
    jsonOut(["success" => true, "id" => $db->lastInsertId()], 201);
}

if ($method === "DELETE") {
    $data = jsonBody();
    $id   = (int)($data["id"] ?? 0);
    if (!$id) jsonOut(["error" => "Invalid id"], 422);

    $db->prepare("DELETE FROM weekly_plan WHERE id = ?")->execute([$id]);
    jsonOut(["success" => true]);
}

jsonOut(["error" => "Method not allowed"], 405);
