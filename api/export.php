<?php
// api/export.php
// Generates a real RFC 5545-compliant .ics file from the weekly plan.
// Does NOT require XHR header since it's a direct browser download.

require __DIR__ . "/../db/connection.php";

$weekStart = $_GET["week_start"] ?? date("Y-m-d", strtotime("last Monday"));

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStart)) {
    http_response_code(400);
    echo "Invalid week_start format.";
    exit;
}

$stmt = $db->prepare("SELECT * FROM weekly_plan WHERE week_start = ?");
$stmt->execute([$weekStart]);
$entries = $stmt->fetchAll();

// Map day name to date offset from Monday
$dayOffset = [
    "Monday"    => 0, "Tuesday" => 1, "Wednesday" => 2,
    "Thursday"  => 3, "Friday"  => 4, "Saturday"  => 5, "Sunday" => 6,
];

$uid = fn() => bin2hex(random_bytes(8)) . "@devotional";
$dtstamp = gmdate("Ymd\THis\Z");

$events = "";
foreach ($entries as $entry) {
    $offset   = $dayOffset[$entry["day"]] ?? 0;
    $date     = date("Ymd", strtotime($weekStart . " +{$offset} days"));
    $summary  = implode(" | ", array_filter([
        $entry["social"]    ? "🤝 " . $entry["social"]    : null,
        $entry["spiritual"] ? "🙏 " . $entry["spiritual"] : null,
        $entry["physical"]  ? "💪 " . $entry["physical"]  : null,
        $entry["learning"]  ? "📚 " . $entry["learning"]  : null,
    ]));
    $description = implode("\\n", array_filter([
        $entry["social"]    ? "Social: "    . $entry["social"]    : null,
        $entry["spiritual"] ? "Spiritual: " . $entry["spiritual"] : null,
        $entry["physical"]  ? "Physical: "  . $entry["physical"]  : null,
        $entry["learning"]  ? "Learning: "  . $entry["learning"]  : null,
    ]));

    if (!$summary) continue; // skip empty entries

    $events .= "BEGIN:VEVENT\r\n";
    $events .= "UID:" . $uid() . "\r\n";
    $events .= "DTSTAMP:{$dtstamp}\r\n";
    $events .= "DTSTART;VALUE=DATE:{$date}\r\n";
    $events .= "DTEND;VALUE=DATE:{$date}\r\n";
    $events .= "SUMMARY:" . addcslashes($summary, ",;\\") . "\r\n";
    $events .= "DESCRIPTION:" . addcslashes($description, ",;\\") . "\r\n";
    $events .= "END:VEVENT\r\n";
}

$filename = "weekly_plan_{$weekStart}.ics";

header("Content-Type: text/calendar; charset=utf-8");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header("Cache-Control: no-cache");

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//Personal Improvement Devotional//EN\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "METHOD:PUBLISH\r\n";
echo $events;
echo "END:VCALENDAR\r\n";
