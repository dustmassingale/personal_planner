<?php
require_once __DIR__ . '/weekly_plan.php';
// weekly_plan.php defines PDO/fallback helpers; reuse them by requiring it

// Expect GET param week_start=YYYY-MM-DD
$week_start = $_GET['week_start'] ?? null;
if (!$week_start) { http_response_code(400); echo 'week_start required'; exit; }

// Reuse read_fallback_wp and $pdo if available by reloading connection
try { $pdo = get_db(); } catch (Exception $e) { $pdo = null; }

// Fetch entries for the week
if ($pdo) {
  $stmt = $pdo->prepare("SELECT id, week_start, day, social, spiritual, physical, learning FROM weekly_plan WHERE week_start = :ws ORDER BY day");
  $stmt->execute([':ws'=>$week_start]);
  $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $m = read_fallback_wp();
  $entries = array_values(array_filter($m['weeklyPlan'], function($i) use ($week_start){ return ($i['week_start'] ?? '') === $week_start; }));
}

// Build ICS
function formatDateForIcs($ymd) {
  // expects YYYY-MM-DD -> YYYYMMDD
  return str_replace('-', '', $ymd);
}

$now = new DateTime('now', new DateTimeZone('UTC'));
$prodid = '-//Personal Planner//EN';
$uidTime = $now->format('Ymd\THis\Z');
$filename = "weekly-plan-{$week_start}.ics";

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$lines = [];
$lines[] = 'BEGIN:VCALENDAR';
$lines[] = 'VERSION:2.0';
$lines[] = 'PRODID:' . $prodid;
$lines[] = 'CALSCALE:GREGORIAN';

foreach ($entries as $e) {
  $dtstart = formatDateForIcs($e['day']);
  // For all-day event, DTEND is next day
  $d = new DateTime($e['day']); $d->modify('+1 day');
  $dtend = $d->format('Ymd');
  $summary = trim(($e['social'] ?: '') . ' ' . ($e['spiritual'] ?: '') . ' ' . ($e['physical'] ?: '') . ' ' . ($e['learning'] ?: ''));
  if ($summary === '') $summary = 'Planned activities';
  $uid = ($e['id'] ?? uniqid()) . '@personal_planner';

  $lines[] = 'BEGIN:VEVENT';
  $lines[] = 'UID:' . $uid;
  $lines[] = 'DTSTAMP:' . $uidTime;
  $lines[] = 'DTSTART;VALUE=DATE:' . $dtstart;
  $lines[] = 'DTEND;VALUE=DATE:' . $dtend;
  $lines[] = 'SUMMARY:' . addcslashes($summary, "\n\r");
  $lines[] = 'END:VEVENT';
}

$lines[] = 'END:VCALENDAR';

echo implode("\r\n", $lines);
