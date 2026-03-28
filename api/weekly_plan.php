<?php
require_once __DIR__ . '/../db/connection.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

try { $pdo = get_db(); } catch (Exception $e) { $pdo = null; }
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

function fallback_path_wp() { return __DIR__ . '/../db/fallback.json'; }
function read_fallback_wp() { $p = fallback_path_wp(); if (!file_exists($p)) return ['weeklyPlan'=>[]]; $s = file_get_contents($p); return json_decode($s, true) ?: ['weeklyPlan'=>[]]; }
function write_fallback_wp($data) { file_put_contents(fallback_path_wp(), json_encode($data, JSON_PRETTY_PRINT)); }

try {
  switch ($method) {
    case 'GET':
      if ($pdo) {
        $stmt = $pdo->query("SELECT id, week_start, day, social, spiritual, physical, learning FROM weekly_plan ORDER BY week_start DESC, id DESC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
      } else {
        $m = read_fallback_wp();
        echo json_encode($m['weeklyPlan'] ?? []);
      }
      break;

    case 'POST':
      // create entries (accepts either single entry or array)
      $entries = isset($input[0]) ? $input : [$input];
      $created = [];
      foreach ($entries as $e) {
        $week_start = $e['week_start'] ?? null;
        $day = $e['day'] ?? null;
        if (!$week_start || !$day) { http_response_code(400); echo json_encode(['error'=>'week_start and day required']); exit; }
        $social = $e['social'] ?? '';
        $spiritual = $e['spiritual'] ?? '';
        $physical = $e['physical'] ?? '';
        $learning = $e['learning'] ?? '';
        if ($pdo) {
          $stmt = $pdo->prepare("INSERT INTO weekly_plan (week_start, day, social, spiritual, physical, learning) VALUES (:week_start,:day,:social,:spiritual,:physical,:learning)");
          $stmt->execute([':week_start'=>$week_start,':day'=>$day,':social'=>$social,':spiritual'=>$spiritual,':physical'=>$physical,':learning'=>$learning]);
          $id = $pdo->lastInsertId();
          $stmt = $pdo->prepare("SELECT id, week_start, day, social, spiritual, physical, learning FROM weekly_plan WHERE id = :id");
          $stmt->execute([':id'=>$id]);
          $created[] = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
          $m = read_fallback_wp(); $id = time().rand(1000,9999);
          $item = ['id'=>$id,'week_start'=>$week_start,'day'=>$day,'social'=>$social,'spiritual'=>$spiritual,'physical'=>$physical,'learning'=>$learning];
          $m['weeklyPlan'][] = $item; write_fallback_wp($m); $created[] = $item;
        }
      }
      echo json_encode(count($created)==1 ? $created[0] : $created);
      break;

    case 'PUT':
      parse_str($_SERVER['QUERY_STRING'], $qs);
      $id = $qs['id'] ?? null;
      if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); break; }
      $week_start = $input['week_start'] ?? null;
      $day = $input['day'] ?? null;
      if (!$week_start || !$day) { http_response_code(400); echo json_encode(['error'=>'week_start and day required']); break; }
      $social = $input['social'] ?? '';
      $spiritual = $input['spiritual'] ?? '';
      $physical = $input['physical'] ?? '';
      $learning = $input['learning'] ?? '';
      if ($pdo) {
        $stmt = $pdo->prepare("UPDATE weekly_plan SET week_start=:week_start, day=:day, social=:social, spiritual=:spiritual, physical=:physical, learning=:learning WHERE id = :id");
        $stmt->execute([':week_start'=>$week_start,':day'=>$day,':social'=>$social,':spiritual'=>$spiritual,':physical'=>$physical,':learning'=>$learning,':id'=>$id]);
        echo json_encode(['ok'=>true]);
      } else {
        $m = read_fallback_wp(); $found=false;
        foreach ($m['weeklyPlan'] as &$item) { if ($item['id']==$id) { $item = ['id'=>$id,'week_start'=>$week_start,'day'=>$day,'social'=>$social,'spiritual'=>$spiritual,'physical'=>$physical,'learning'=>$learning]; $found=true; break; } }
        if (!$found) { http_response_code(404); echo json_encode(['error'=>'not found']); break; }
        write_fallback_wp($m); echo json_encode(['ok'=>true]);
      }
      break;

    case 'DELETE':
      parse_str($_SERVER['QUERY_STRING'], $qs);
      $id = $qs['id'] ?? null; if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); break; }
      if ($pdo) { $stmt = $pdo->prepare("DELETE FROM weekly_plan WHERE id = :id"); $stmt->execute([':id'=>$id]); echo json_encode(['ok'=>true]); }
      else { $m = read_fallback_wp(); $before=count($m['weeklyPlan']); $m['weeklyPlan']=array_filter($m['weeklyPlan'], function($i) use ($id){ return $i['id']!=$id; }); if (count($m['weeklyPlan'])==$before){ http_response_code(404); echo json_encode(['error'=>'not found']); break; } write_fallback_wp($m); echo json_encode(['ok'=>true]); }
      break;

    default:
      http_response_code(405); echo json_encode(['error'=>'Method not allowed']);
  }
} catch (Exception $e) { http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
