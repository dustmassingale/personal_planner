<?php
require_once __DIR__ . '/../db/connection.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

try {
    $pdo = get_db();
} catch (Exception $e) {
    $pdo = null;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Fallback to a dedicated fallback file (do NOT touch agent memory)
function fallback_path() { return __DIR__ . '/../db/fallback.json'; }
function read_fallback() {
    $p = fallback_path();
    if (!file_exists($p)) return ['values'=>[]];
    $s = file_get_contents($p);
    return json_decode($s, true) ?: ['values'=>[]];
}
function write_fallback($data) {
    file_put_contents(fallback_path(), json_encode($data, JSON_PRETTY_PRINT));
}

try {
    switch ($method) {
        case 'GET':
            if ($pdo) {
                $stmt = $pdo->query("SELECT id, name, description, selected FROM values ORDER BY id DESC");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($rows);
            } else {
                $m = read_fallback();
                echo json_encode($m['values'] ?? []);
            }
            break;

        case 'POST':
            $name = trim($input['name'] ?? '');
            $description = trim($input['description'] ?? '');
            if ($name === '') { http_response_code(400); echo json_encode(['error'=>'name required']); break; }
            if ($pdo) {
                $stmt = $pdo->prepare("INSERT INTO values (name, description, selected) VALUES (:name, :description, :selected)");
                $stmt->execute([':name'=>$name, ':description'=>$description, ':selected'=>(isset($input['selected']) && $input['selected'])?1:0]);
                $id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT id, name, description, selected FROM values WHERE id = :id");
                $stmt->execute([':id'=>$id]);
                echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            } else {
                $m = read_fallback();
                $id = time() . rand(1000,9999);
                $item = ['id'=>$id, 'name'=>$name, 'description'=>$description, 'selected'=>(isset($input['selected']) && $input['selected'])?1:0];
                $m['values'][] = $item;
                write_fallback($m);
                echo json_encode($item);
            }
            break;

        case 'PUT':
            parse_str($_SERVER['QUERY_STRING'], $qs);
            $id = $qs['id'] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); break; }
            $name = trim($input['name'] ?? '');
            $description = trim($input['description'] ?? '');
            if ($name === '') { http_response_code(400); echo json_encode(['error'=>'name required']); break; }
            if ($pdo) {
                $stmt = $pdo->prepare("UPDATE values SET name = :name, description = :description, selected = :selected WHERE id = :id");
                $stmt->execute([':name'=>$name, ':description'=>$description, ':selected'=> (isset($input['selected']) && $input['selected'])?1:0, ':id'=>$id]);
                echo json_encode(['ok'=>true]);
            } else {
                $m = read_fallback();
                $found = false;
                foreach ($m['values'] as &$v) {
                    if ($v['id'] == $id) { $v['name']=$name; $v['description']=$description; $v['selected']=isset($input['selected'])?($input['selected']?1:0):0; $found=true; break; }
                }
                if (!$found) { http_response_code(404); echo json_encode(['error'=>'not found']); break; }
                write_fallback($m);
                echo json_encode(['ok'=>true]);
            }
            break;

        case 'DELETE':
            parse_str($_SERVER['QUERY_STRING'], $qs);
            $id = $qs['id'] ?? null;
            if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); break; }
            if ($pdo) {
                $stmt = $pdo->prepare("DELETE FROM values WHERE id = :id");
                $stmt->execute([':id'=>$id]);
                echo json_encode(['ok'=>true]);
            } else {
                $m = read_fallback();
                $before = count($m['values']);
                $m['values'] = array_filter($m['values'], function($v) use ($id) { return $v['id'] != $id; });
                if (count($m['values']) === $before) { http_response_code(404); echo json_encode(['error'=>'not found']); break; }
                write_fallback($m);
                echo json_encode(['ok'=>true]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error'=>'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
